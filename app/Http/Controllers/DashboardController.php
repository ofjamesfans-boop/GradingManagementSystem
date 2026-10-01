<?php

namespace App\Http\Controllers;

use App\Models\{ClassAssignment, Enrollment, Grade, SchoolYear, Student, Subject};
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $currentPeriod = SchoolYear::activateCurrent(now());
        $grades = Grade::query();
        $students = Student::query();
        $subjects = Subject::query();

        if ($user->isInstructor()) {
            $grades->whereHas('enrollment.classAssignment.instructor', fn ($query) => $query->where('user_id', $user->id));
            $students->whereHas('enrollments.classAssignment.instructor', fn ($query) => $query->where('user_id', $user->id));
            $subjects->whereHas('classAssignments.instructor', fn ($query) => $query->where('user_id', $user->id));
        }

        if ($user->isStudent()) {
            $grades->whereHas('enrollment.student', fn ($query) => $query->where('user_id', $user->id))->where('status', 'released');
            $students->where('user_id', $user->id);
            $subjects->whereHas('classAssignments.enrollments.student', fn ($query) => $query->where('user_id', $user->id));
        }

        $studentClasses = collect();
        if ($user->isStudent()) {
            $studentClasses = Enrollment::with([
                'classAssignment.subject',
                'classAssignment.instructor',
                'classAssignment.section',
                'classAssignment.schoolYear',
                'grade',
            ])->whereHas('student', fn ($query) => $query->where('user_id', $user->id))
                ->where('status', 'active')
                ->whereHas('classAssignment', fn ($query) => $query->where('school_year_id', $currentPeriod->id)->where('status', 'active'))
                ->get();
        }

        $instructorClasses = collect();
        if ($user->isInstructor()) {
            $instructorClasses = ClassAssignment::with([
                'subject',
                'section',
                'schoolYear',
                'enrollments.grade',
            ])->withCount(['enrollments' => fn ($query) => $query->where('status', 'active')])
                ->whereHas('instructor', fn ($query) => $query->where('user_id', $user->id))
                ->where('school_year_id', $currentPeriod->id)
                ->where('status', 'active')
                ->get();
        }

        $recordCount = (clone $grades)->count();
        $statusCounts = collect(['draft', 'submitted', 'released'])->mapWithKeys(fn ($status) => [$status => (clone $grades)->where('status', $status)->count()]);
        $gradeBands = [
            '1.00-1.99' => (clone $grades)->whereBetween('final_grade', [1, 1.99])->count(),
            '2.00-2.99' => (clone $grades)->whereBetween('final_grade', [2, 2.99])->count(),
            '3.00-3.24' => (clone $grades)->whereBetween('final_grade', [3, 3.24])->count(),
            '3.25-5.00' => (clone $grades)->whereBetween('final_grade', [3.25, 5])->count(),
            'Legacy >5' => (clone $grades)->where('final_grade', '>', 5)->count(),
        ];
        return view('dashboard', [
            'studentCount' => (clone $students)->where('status', 'active')->count(),
            'subjectCount' => (clone $subjects)->count(),
            'recordCount' => $recordCount,
            'records' => (clone $grades)->with('enrollment.student', 'enrollment.classAssignment.subject')->latest()->take(6)->get(),
            'statusCounts' => $statusCounts,
            'gradeBands' => $gradeBands,
            'chartMaximum' => max(1, ...array_values($gradeBands)),
            'studentClasses' => $studentClasses,
            'instructorClasses' => $instructorClasses,
        ]);
    }
}

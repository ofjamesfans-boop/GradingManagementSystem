<?php

namespace App\Http\Controllers;

use App\Models\{ActivityLog, ClassAssignment, Enrollment, Grade, Instructor, SchoolYear, Section, Subject};
use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class GradeRecordController extends Controller
{
    private function query(Request $request)
    {
        $query = Grade::with('enrollment.student', 'enrollment.classAssignment.subject', 'enrollment.classAssignment.section');

        if ($request->user()->isInstructor()) {
            $query->whereHas('enrollment.classAssignment.instructor', fn ($instructor) => $instructor->where('user_id', $request->user()->id));
        }
        if ($request->user()->isStudent()) {
            $query->whereHas('enrollment.student', fn ($student) => $student->where('user_id', $request->user()->id))
                ->where('status', 'released');
        }

        return $query;
    }

    public function index(Request $request): View
    {
        if ($request->user()->isStudent()) {
            $enrollments = Enrollment::with('classAssignment.subject', 'classAssignment.schoolYear', 'grade')
                ->whereHas('student', fn ($student) => $student->where('user_id', $request->user()->id))
                ->where(fn ($query) => $query->where('status', 'active')
                    ->orWhereHas('grade', fn ($grade) => $grade->where('status', 'released')))
                ->get()
                ->sort(function (Enrollment $first, Enrollment $second): int {
                    $firstPeriod = $first->classAssignment->schoolYear;
                    $secondPeriod = $second->classAssignment->schoolYear;
                    $yearOrder = strcmp($secondPeriod->school_year, $firstPeriod->school_year);
                    if ($yearOrder !== 0) return $yearOrder;
                    $semesterOrder = ['1st Semester' => 1, '2nd Semester' => 2, 'Summer' => 3];
                    $termOrder = ($semesterOrder[$secondPeriod->semester] ?? 0) <=> ($semesterOrder[$firstPeriod->semester] ?? 0);
                    if ($termOrder !== 0) return $termOrder;
                    return strnatcasecmp($first->classAssignment->subject->subject_code, $second->classAssignment->subject->subject_code);
                })
                ->values();

            return view('grades.student-index', compact('enrollments'));
        }

        if ($request->user()->isAdmin()) {
            $matchingGradeIds = $this->filtered($request)->select('grades.id');
            $instructors = Instructor::whereHas('classAssignments.enrollments.grade', fn ($grade) => $grade
                ->whereIn('grades.id', $matchingGradeIds))
                ->orderBy('last_name')
                ->paginate(10)
                ->withQueryString();

            $records = $this->filtered($request)
                ->whereHas('enrollment.classAssignment', fn ($assignment) => $assignment
                    ->whereIn('instructor_id', $instructors->pluck('id')))
                ->with('enrollment.classAssignment.instructor', 'enrollment.classAssignment.schoolYear')
                ->get();
            $gradeGroups = $records
                ->groupBy(fn (Grade $grade) => $grade->enrollment->classAssignment->instructor_id)
                ->map(fn ($grades) => $grades->groupBy(fn (Grade $grade) => $grade->enrollment->class_assignment_id));
            $subjects = Subject::orderBy('subject_code')->get();
            $sections = Section::orderBy('section_name')->get();
            $schoolYears = SchoolYear::orderByDesc('id')->get();

            return view('grades.admin-index', compact('instructors', 'gradeGroups', 'subjects', 'sections', 'schoolYears'));
        }

        $records = $this->filtered($request)->latest()->paginate(15)->withQueryString();
        $subjects = Subject::orderBy('subject_code')->get();
        $sections = Section::orderBy('section_name')->get();
        $schoolYears = SchoolYear::orderByDesc('id')->get();

        return view('grades.index', compact('records', 'subjects', 'sections', 'schoolYears'));
    }

    public function filtered(Request $request)
    {
        return $this->query($request)
            ->when($request->status, fn ($query, $status) => $query->where('status', $status))
            ->when($request->subject_id, fn ($query, $id) => $query->whereHas('enrollment.classAssignment', fn ($assignment) => $assignment->where('subject_id', $id)))
            ->when($request->section_id, fn ($query, $id) => $query->whereHas('enrollment.classAssignment', fn ($assignment) => $assignment->where('section_id', $id)))
            ->when($request->school_year_id, fn ($query, $id) => $query->whereHas('enrollment.classAssignment', fn ($assignment) => $assignment->where('school_year_id', $id)))
            ->when($request->search, fn ($query, $search) => $query->whereHas('enrollment.student', fn ($student) => $student
                ->where('student_number', 'like', "%$search%")
                ->orWhere('first_name', 'like', "%$search%")
                ->orWhere('last_name', 'like', "%$search%")));
    }

    public function create(Request $request): View
    {
        abort_if($request->user()->isStudent(), 403);
        $currentPeriod = SchoolYear::activateCurrent(now());
        $assignments = ClassAssignment::with('subject', 'section')
            ->when($request->user()->isInstructor(), fn ($query) => $query
                ->whereHas('instructor', fn ($instructor) => $instructor->where('user_id', $request->user()->id)))
            ->where('school_year_id', $currentPeriod->id)
            ->where('status', 'active')
            ->get();

        $assignment = $assignments->firstWhere('id', (int) $request->assignment_id);
        $enrollments = $assignment
            ? $assignment->enrollments()->with('student')
                ->where('status', 'active')
                ->whereHas('student', fn ($student) => $student->where('status', 'active'))
                ->whereDoesntHave('grade')
                ->get()
            : collect();

        return view('grades.form', [
            'grade' => new Grade,
            'assignments' => $assignments,
            'assignment' => $assignment,
            'enrollments' => $enrollments,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_if($request->user()->isStudent(), 403);
        $data = $this->data($request);
        $enrollment = Enrollment::with('student', 'classAssignment.subject', 'classAssignment.instructor')->findOrFail($data['enrollment_id']);
        $this->assignmentEditable($request, $enrollment->classAssignment);
        abort_unless($enrollment->status === 'active' && $enrollment->student->status === 'active', 403);

        $average = Grade::compute($data['prelim'], $data['midterm'], $data['final']);
        Grade::create($data + [
            'final_grade' => $average,
            'remarks' => Grade::remark($average),
            'status' => 'draft',
            'encoded_by' => $request->user()->id,
        ]);
        ActivityLog::record('grade.encoded', "{$request->user()->name} encoded the grade of {$enrollment->student->student_number} in {$enrollment->classAssignment->subject->subject_code}.");

        return redirect()->route('classes.show', $enrollment->classAssignment)->with('status', 'Draft grade saved.');
    }

    public function show(Request $request, Grade $grade): View
    {
        $this->gradeAccess($request, $grade);
        $grade->load('enrollment.student', 'enrollment.classAssignment.subject', 'enrollment.classAssignment.section', 'encoder');

        return view('grades.show', ['record' => $grade]);
    }

    public function edit(Request $request, Grade $grade): View
    {
        abort_if($request->user()->isStudent(), 403);
        $this->gradeAccess($request, $grade);
        abort_unless($grade->status === 'draft', 403);
        if ($request->user()->isInstructor()) {
            $this->assignmentEditable($request, $grade->enrollment->classAssignment);
        }

        $grade->load('enrollment.classAssignment.subject', 'enrollment.classAssignment.section');

        return view('grades.form', [
            'grade' => $grade,
            'assignment' => $grade->enrollment->classAssignment,
            'assignments' => collect([$grade->enrollment->classAssignment]),
            'enrollments' => collect([$grade->enrollment]),
        ]);
    }

    public function update(Request $request, Grade $grade): RedirectResponse
    {
        abort_if($request->user()->isStudent(), 403);
        $this->gradeAccess($request, $grade);
        abort_unless($grade->status === 'draft', 403);
        $data = $this->data($request, $grade);
        abort_unless((int) $data['enrollment_id'] === $grade->enrollment_id, 403);
        if ($request->user()->isInstructor()) {
            $this->assignmentEditable($request, $grade->enrollment->classAssignment);
        }

        $average = Grade::compute($data['prelim'], $data['midterm'], $data['final']);
        $grade->update($data + ['final_grade' => $average, 'remarks' => Grade::remark($average)]);
        ActivityLog::record('grade.updated', "{$request->user()->name} updated a draft grade.");

        return redirect()->route('grades.index')->with('status', 'Draft grade updated.');
    }

    public function submit(Request $request, Grade $grade): RedirectResponse
    {
        abort_if($request->user()->isStudent(), 403);
        $this->gradeAccess($request, $grade);
        abort_unless($grade->status === 'draft', 403);
        if ($request->user()->isInstructor()) {
            $this->assignmentEditable($request, $grade->enrollment->classAssignment);
        }
        abort_if(collect(['prelim', 'midterm', 'final'])->contains(fn ($field) => $grade->$field === null), 422);

        $grade->update(['status' => 'submitted']);
        ActivityLog::record('grade.submitted', "{$request->user()->name} submitted grade #{$grade->id}.");

        return back()->with('status', 'Grade submitted.');
    }

    public function release(Request $request, Grade $grade): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);
        abort_unless($grade->status === 'submitted', 422);
        $grade->update(['status' => 'released']);
        ActivityLog::record('grade.released', "{$request->user()->name} released grade #{$grade->id}.");

        return back()->with('status', 'Grade released to student.');
    }

    public function reopen(Request $request, Grade $grade): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);
        abort_if($grade->status === 'draft', 422);
        $grade->update(['status' => 'draft']);
        ActivityLog::record('grade.reopened', "{$request->user()->name} reopened grade #{$grade->id}.");

        return back()->with('status', 'Grade reopened as Draft.');
    }

    private function data(Request $request, ?Grade $grade = null): array
    {
        return $request->validate([
            'enrollment_id' => ['required', 'exists:enrollments,id', Rule::unique('grades', 'enrollment_id')->ignore($grade)],
            'prelim' => ['required', Rule::in(Grade::scoreOptions())],
            'midterm' => ['required', Rule::in(Grade::scoreOptions())],
            'final' => ['required', Rule::in(Grade::scoreOptions())],
        ]);
    }

    private function assignmentAccess(Request $request, ClassAssignment $assignment): void
    {
        if ($request->user()->isInstructor()) {
            abort_unless($assignment->instructor?->user_id === $request->user()->id, 403);
        }
    }

    private function assignmentEditable(Request $request, ClassAssignment $assignment): void
    {
        $this->assignmentAccess($request, $assignment);
        $currentPeriod = SchoolYear::activateCurrent(now());
        abort_unless($assignment->status === 'active' && $assignment->school_year_id === $currentPeriod->id, 403);
    }

    private function gradeAccess(Request $request, Grade $grade): void
    {
        $grade->loadMissing('enrollment.classAssignment.instructor', 'enrollment.student');
        $this->assignmentAccess($request, $grade->enrollment->classAssignment);
        if ($request->user()->isStudent()) {
            abort_unless($grade->status === 'released' && $grade->enrollment->student->user_id === $request->user()->id, 403);
        }
    }
}


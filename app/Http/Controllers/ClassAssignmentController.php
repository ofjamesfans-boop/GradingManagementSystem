<?php

namespace App\Http\Controllers;

use App\Models\{ClassAssignment, Instructor, SchoolYear, Section, Student, Subject};
use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ClassAssignmentController extends Controller
{
    public function index(Request $request): View
    {
        $currentPeriod = SchoolYear::activateCurrent(now());
        $showAll = $request->query('period') === 'all';
        $assignments = ClassAssignment::with('instructor', 'subject', 'section', 'schoolYear')
            ->withCount(['enrollments' => fn ($query) => $query->where('status', 'active')])
            ->where('status', 'active')
            ->when($request->user()->isInstructor(), fn ($query) => $query->whereHas('instructor', fn ($instructor) => $instructor->where('user_id', $request->user()->id)))
            ->when(! $showAll, fn ($query) => $query->where('school_year_id', $currentPeriod->id))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('classes.index', compact('assignments', 'currentPeriod', 'showAll'));
    }

    public function create(): View
    {
        $currentPeriod = SchoolYear::activateCurrent(now());

        return view('classes.form', [
            'instructors' => Instructor::where('status', 'active')->get(),
            'subjects' => Subject::where('status', 'active')->get(),
            'sections' => Section::active()->get(),
            'currentPeriod' => $currentPeriod,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $currentPeriod = SchoolYear::activateCurrent(now());
        $data = $request->validate([
            'instructor_id' => ['required', Rule::exists('instructors', 'id')->where('status', 'active')],
            'subject_id' => [
                'required',
                Rule::exists('subjects', 'id')->where('status', 'active'),
                Rule::unique('class_assignments', 'subject_id')
                    ->where('section_id', $request->input('section_id'))
                    ->where('school_year_id', $currentPeriod->id),
            ],
            'section_id' => ['required', Rule::exists('sections', 'id')->where('status', 'active')],
        ]);

        DB::transaction(function () use ($data, $currentPeriod): void {
            $assignment = ClassAssignment::create($data + [
                'school_year_id' => $currentPeriod->id,
                'status' => 'active',
            ]);

            Student::active()->where('section_id', $assignment->section_id)
                ->pluck('id')
                ->each(fn ($studentId) => $assignment->enrollments()->firstOrCreate(
                    ['student_id' => $studentId],
                    ['status' => 'active']
                ));
        });

        return redirect()->route('classes.index')->with('status', 'Class assigned and section students enrolled.');
    }

    public function show(Request $request, ClassAssignment $class): View
    {
        if ($request->user()->isInstructor()) {
            abort_unless($class->status === 'active' && $class->instructor?->user_id === $request->user()->id, 403);
        }

        $currentPeriod = SchoolYear::activateCurrent(now());
        $class->load(
            'instructor',
            'subject',
            'section',
            'schoolYear',
            'enrollments.student',
            'enrollments.grade'
        );

        return view('classes.show', [
            'assignment' => $class,
            'currentPeriod' => $currentPeriod,
            'canEditGrades' => $request->user()->isInstructor()
                && $class->school_year_id === $currentPeriod->id
                && $class->status === 'active',
        ]);
    }
}


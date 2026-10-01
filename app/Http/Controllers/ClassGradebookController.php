<?php

namespace App\Http\Controllers;

use App\Models\{ActivityLog, ClassAssignment, Grade, SchoolYear};
use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ClassGradebookController extends Controller
{
    public function index(Request $request): View
    {
        $currentPeriod = SchoolYear::activateCurrent(now());
        $classes = ClassAssignment::with('subject', 'section', 'schoolYear')
            ->withCount([
                'enrollments as students_count' => fn ($query) => $query->where('status', 'active')
                    ->whereHas('student', fn ($student) => $student->where('status', 'active')),
                'enrollments as drafts_count' => fn ($query) => $query->where('status', 'active')
                    ->whereHas('grade', fn ($grade) => $grade->where('status', 'draft')),
                'enrollments as submitted_count' => fn ($query) => $query->where('status', 'active')
                    ->whereHas('grade', fn ($grade) => $grade->where('status', 'submitted')),
                'enrollments as released_count' => fn ($query) => $query->where('status', 'active')
                    ->whereHas('grade', fn ($grade) => $grade->where('status', 'released')),
            ])
            ->whereHas('instructor', fn ($query) => $query->where('user_id', $request->user()->id))
            ->where('school_year_id', $currentPeriod->id)
            ->where('status', 'active')
            ->orderBy('id')
            ->get();

        return view('grades.class-picker', compact('classes', 'currentPeriod'));
    }

    public function save(Request $request, ClassAssignment $class): RedirectResponse
    {
        $this->authorizeCurrentClass($request, $class);
        $data = $request->validate([
            'grades' => ['required', 'array'],
            'grades.*' => ['required', 'array'],
            'grades.*.prelim' => ['nullable', Rule::in(Grade::scoreOptions())],
            'grades.*.midterm' => ['nullable', Rule::in(Grade::scoreOptions())],
            'grades.*.final' => ['nullable', Rule::in(Grade::scoreOptions())],
        ]);

        DB::transaction(function () use ($class, $request, $data): void {
            $enrollments = $class->enrollments()
                ->where('status', 'active')
                ->whereHas('student', fn ($query) => $query->where('status', 'active'))
                ->with('student')
                ->get()
                ->keyBy('id');

            foreach ($data['grades'] as $enrollmentId => $values) {
                $enrollment = $enrollments->get((int) $enrollmentId);
                if (! $enrollment) {
                    throw ValidationException::withMessages(['grades' => 'One or more students are not enrolled in this class.']);
                }

                $grade = Grade::where('enrollment_id', $enrollment->id)->lockForUpdate()->first();
                if ($grade && $grade->status !== 'draft') {
                    throw ValidationException::withMessages(['grades' => "{$enrollment->student->student_number} is already submitted or released."]);
                }

                $scores = [];
                foreach (['prelim', 'midterm', 'final'] as $component) {
                    $scores[$component] = ($values[$component] ?? '') === '' ? null : $values[$component];
                }

                if (! $grade && collect($scores)->every(fn ($score) => $score === null)) {
                    continue;
                }

                $complete = collect($scores)->every(fn ($score) => $score !== null);
                $average = $complete ? Grade::compute($scores['prelim'], $scores['midterm'], $scores['final']) : null;
                Grade::updateOrCreate(
                    ['enrollment_id' => $enrollment->id],
                    $scores + [
                        'final_grade' => $average,
                        'remarks' => $complete ? Grade::remark($average) : null,
                        'status' => 'draft',
                        'encoded_by' => $request->user()->id,
                    ]
                );
                ActivityLog::record(
                    'grade.updated',
                    "{$request->user()->name} saved a draft grade for {$enrollment->student->student_number} in {$class->subject->subject_code}."
                );
            }
        });

        return redirect()->route('classes.show', $class)->with('status', 'Draft grades saved.');
    }

    public function submit(Request $request, ClassAssignment $class): RedirectResponse
    {
        $this->authorizeCurrentClass($request, $class);

        DB::transaction(function () use ($class, $request): void {
            $enrollments = $class->enrollments()
                ->where('status', 'active')
                ->whereHas('student', fn ($query) => $query->where('status', 'active'))
                ->with('student')
                ->get();

            if ($enrollments->isEmpty()) {
                throw ValidationException::withMessages(['gradebook' => 'There are no active students in this class.']);
            }

            $grades = Grade::whereIn('enrollment_id', $enrollments->pluck('id'))
                ->lockForUpdate()
                ->get()
                ->keyBy('enrollment_id');

            foreach ($enrollments as $enrollment) {
                $grade = $grades->get($enrollment->id);
                if (! $grade || collect(['prelim', 'midterm', 'final'])->contains(fn ($field) => $grade->$field === null)) {
                    throw ValidationException::withMessages([
                        'gradebook' => "Complete all grades for {$enrollment->student->student_number} before submitting.",
                    ]);
                }
            }

            $draftIds = $grades->where('status', 'draft')->pluck('id');
            if ($draftIds->isEmpty()) {
                throw ValidationException::withMessages(['gradebook' => 'No draft grades are ready to submit.']);
            }

            Grade::whereIn('id', $draftIds)->update(['status' => 'submitted']);
            ActivityLog::record(
                'class.grades.submitted',
                "{$request->user()->name} submitted {$draftIds->count()} grades in {$class->subject->subject_code} / {$class->section->section_name}."
            );
        });

        return redirect()->route('classes.show', $class)->with('status', 'Class grades submitted for review.');
    }

    private function authorizeCurrentClass(Request $request, ClassAssignment $class): void
    {
        abort_unless($class->instructor?->user_id === $request->user()->id, 403);
        $currentPeriod = SchoolYear::activateCurrent(now());
        abort_unless($class->status === 'active' && $class->school_year_id === $currentPeriod->id, 403);
    }
}


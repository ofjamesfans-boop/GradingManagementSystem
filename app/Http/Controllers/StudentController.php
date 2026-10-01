<?php

namespace App\Http\Controllers;

use App\Models\{ActivityLog, ClassAssignment, Enrollment, SchoolYear, Section, Student, User};
use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\Support\Facades\{DB, Hash};
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->user()->isAdmin() ? $request->input('status', 'active') : 'active';
        $query = Student::with('section')
            ->when($request->user()->isInstructor(), fn ($query) => $query->whereHas('enrollments', fn ($enrollment) => $enrollment
                ->where('status', 'active')
                ->whereHas('classAssignment.instructor', fn ($instructor) => $instructor->where('user_id', $request->user()->id))))
            ->when($status !== 'all', fn ($query) => $query->where('status', $status))
            ->when($request->section_id, fn ($query, $id) => $query->where('section_id', $id))
            ->when($request->search, fn ($query, $search) => $query->where(fn ($match) => $match
                ->where('student_number', 'like', "%$search%")
                ->orWhere('first_name', 'like', "%$search%")
                ->orWhere('middle_name', 'like', "%$search%")
                ->orWhere('last_name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%")
                ->orWhereHas('section', fn ($section) => $section->where('section_name', 'like', "%$search%"))));

        $students = $query->orderBy('last_name')->paginate(10)->withQueryString();
        $sections = Section::active()->orderBy('section_name')->get();

        return view('students.index', compact('students', 'status', 'sections'));
    }

    public function create(): View
    {
        return view('students.form', [
            'student' => new Student,
            'sections' => Section::active()->orderBy('section_name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        DB::transaction(function () use ($data, $request): void {
            $user = User::create([
                'name' => trim($data['first_name'].' '.$data['last_name']),
                'email' => $data['email'],
                'role' => 'student',
                'status' => 'active',
                'password' => Hash::make(Student::defaultPasswordFor($data['student_number'])),
            ]);
            $student = Student::create($data + ['user_id' => $user->id, 'status' => 'active']);
            $this->syncEnrollments($student);
            ActivityLog::record('student.created', "{$request->user()->name} created student {$student->student_number}.");
        });

        return redirect()->route('students.index')->with('status', 'Student account created with a BCC default password.');
    }

    public function show(Request $request, Student $student): View
    {
        if ($request->user()->isInstructor()) {
            abort_unless($student->enrollments()
                ->where('status', 'active')
                ->whereHas('classAssignment.instructor', fn ($instructor) => $instructor->where('user_id', $request->user()->id))
                ->exists(), 403);
        }

        $student->load('section');
        $enrollments = $student->enrollments()
            ->with('classAssignment.subject', 'classAssignment.schoolYear', 'grade')
            ->when($request->user()->isInstructor(), fn ($query) => $query
                ->whereHas('classAssignment.instructor', fn ($instructor) => $instructor->where('user_id', $request->user()->id)))
            ->get();

        return view('students.show', compact('student', 'enrollments'));
    }

    public function edit(Student $student): View
    {
        return view('students.form', [
            'student' => $student,
            'sections' => Section::active()->orderBy('section_name')->get(),
        ]);
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $data = $this->validated($request, $student);
        DB::transaction(function () use ($data, $student, $request): void {
            $oldSection = $student->section_id;
            $student->update($data);
            $student->user?->update([
                'name' => trim($data['first_name'].' '.$data['last_name']),
                'email' => $data['email'],
            ]);
            $this->syncEnrollments($student, $oldSection);
            ActivityLog::record('student.updated', "{$request->user()->name} updated student {$student->student_number}.");
        });

        return redirect()->route('students.index')->with('status', 'Student and account updated.');
    }

    public function archive(Request $request, Student $student): RedirectResponse
    {
        DB::transaction(function () use ($request, $student): void {
            $student->update(['status' => 'archived']);
            $student->user?->update(['status' => 'inactive']);
            $student->enrollments()->where('status', 'active')->update(['status' => 'inactive']);
            ActivityLog::record('student.archived', "{$request->user()->name} archived student {$student->student_number}.");
        });

        return back()->with('status', 'Student archived.');
    }

    public function restore(Request $request, Student $student): RedirectResponse
    {
        DB::transaction(function () use ($request, $student): void {
            $student->update(['status' => 'active']);
            $student->user?->update(['status' => 'active']);
            $this->syncEnrollments($student);
            ActivityLog::record('student.restored', "{$request->user()->name} restored student {$student->student_number}.");
        });

        return back()->with('status', 'Student restored.');
    }

    private function validated(Request $request, ?Student $student = null): array
    {
        return $request->validate([
            'student_number' => ['required', 'max:50', Rule::unique('students')->ignore($student)],
            'first_name' => 'required|max:100',
            'middle_name' => 'nullable|max:100',
            'last_name' => 'required|max:100',
            'email' => ['required', 'email', Rule::unique('students')->ignore($student), Rule::unique('users')->ignore($student?->user_id)],
            'section_id' => 'nullable|exists:sections,id',
        ]);
    }

    private function syncEnrollments(Student $student, ?int $oldSection = null): void
    {
        $currentPeriod = SchoolYear::activateCurrent(now());
        if ($oldSection && $oldSection !== $student->section_id) {
            Enrollment::where('student_id', $student->id)
                ->where('status', 'active')
                ->whereHas('classAssignment', fn ($query) => $query
                    ->where('section_id', $oldSection)
                    ->where('school_year_id', $currentPeriod->id))
                ->update(['status' => 'inactive']);
        }

        if (! $student->section_id) {
            return;
        }

        ClassAssignment::where('section_id', $student->section_id)
            ->where('school_year_id', $currentPeriod->id)
            ->where('status', 'active')
            ->pluck('id')
            ->each(fn ($assignmentId) => Enrollment::updateOrCreate(
                ['student_id' => $student->id, 'class_assignment_id' => $assignmentId],
                ['status' => 'active']
            ));
    }
}


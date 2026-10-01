<?php

namespace Tests\Feature;

use App\Models\{ClassAssignment, Enrollment, Grade, Instructor, SchoolYear, Section, Student, Subject, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClassGradebookTest extends TestCase
{
    use RefreshDatabase;

    public function test_faculty_grade_entry_has_its_own_page_and_only_assigned_classes(): void
    {
        [$admin, $faculty, $class, $students] = $this->classWithStudents();
        $this->actingAs($faculty)->get('/faculty/grade-entry')
            ->assertOk()
            ->assertSee('Assigned Classes')
            ->assertSee('Open Gradebook')
            ->assertSee("/classes/{$class->id}");
        $this->actingAs($faculty)->get('/classes')->assertOk()->assertSee('My Classes');
        $this->actingAs($admin)->get('/faculty/grade-entry')->assertForbidden();
        $this->actingAs($students[0]->user)->get('/faculty/grade-entry')->assertForbidden();
    }

    public function test_archived_class_disappears_from_faculty_pages(): void
    {
        [, $faculty, $class] = $this->classWithStudents();
        $class->update(['status' => 'archived']);

        $this->actingAs($faculty)->get('/classes')->assertOk()->assertDontSee('ITE101');
        $this->actingAs($faculty)->get('/faculty/grade-entry')->assertOk()->assertDontSee('ITE101');
        $this->actingAs($faculty)->get("/classes/{$class->id}")->assertForbidden();
    }

    public function test_bulk_gradebook_saves_drafts_and_requires_a_complete_roster_before_submission(): void
    {
        [$admin, $faculty, $class, $students] = $this->classWithStudents();
        [$first, $second] = $students;
        $firstEnrollment = $first->enrollments()->firstOrFail();
        $secondEnrollment = $second->enrollments()->firstOrFail();

        $this->actingAs($faculty)->get("/classes/{$class->id}")
            ->assertOk()
            ->assertSee('Save Drafts')
            ->assertSee($first->student_number);

        $this->actingAs($faculty)->put("/classes/{$class->id}/gradebook", [
            'grades' => [
                $firstEnrollment->id => ['prelim' => '1.00', 'midterm' => '1.25', 'final' => '1.50'],
                $secondEnrollment->id => ['prelim' => '2.00', 'midterm' => '', 'final' => ''],
            ],
        ])->assertRedirect("/classes/{$class->id}");

        $firstGrade = Grade::where('enrollment_id', $firstEnrollment->id)->firstOrFail();
        $this->assertSame('draft', $firstGrade->status);
        $this->assertEquals(1.25, $firstGrade->final_grade);
        $this->assertNull(Grade::where('enrollment_id', $secondEnrollment->id)->firstOrFail()->final_grade);

        $this->actingAs($faculty)->post("/classes/{$class->id}/gradebook/submit")
            ->assertSessionHasErrors('gradebook');
        $this->assertSame('draft', $firstGrade->fresh()->status);

        $this->actingAs($faculty)->put("/classes/{$class->id}/gradebook", [
            'grades' => [
                $secondEnrollment->id => ['prelim' => '2.00', 'midterm' => '2.25', 'final' => '2.50'],
            ],
        ])->assertRedirect();

        $this->actingAs($faculty)->post("/classes/{$class->id}/gradebook/submit")
            ->assertRedirect("/classes/{$class->id}");
        $this->assertSame(2, Grade::where('status', 'submitted')->count());
        $this->actingAs($students[0]->user)->get('/grades')->assertDontSee('1.25');
        $this->actingAs($admin)->patch("/grades/{$firstGrade->id}/release")->assertRedirect();
        $this->actingAs($students[0]->user)->get('/grades')->assertSee('1.25');
    }

    public function test_gradebook_rejects_non_quarter_scores(): void
    {
        [, $faculty, $class, $students] = $this->classWithStudents();
        $enrollment = $students[0]->enrollments()->firstOrFail();

        $this->actingAs($faculty)->put("/classes/{$class->id}/gradebook", [
            'grades' => [$enrollment->id => ['prelim' => '1.10', 'midterm' => '2.00', 'final' => '2.25']],
        ])->assertSessionHasErrors("grades.{$enrollment->id}.prelim");
        $this->assertDatabaseCount('grades', 0);
    }

    public function test_gradebook_rejects_malformed_rows(): void
    {
        [, $faculty, $class, $students] = $this->classWithStudents();
        $enrollment = $students[0]->enrollments()->firstOrFail();

        $this->actingAs($faculty)->put("/classes/{$class->id}/gradebook", [
            'grades' => [$enrollment->id => 'invalid'],
        ])->assertSessionHasErrors("grades.{$enrollment->id}");
        $this->assertDatabaseCount('grades', 0);
    }

    public function test_faculty_cannot_change_submitted_or_unassigned_class_grades(): void
    {
        [, $faculty, $class, $students] = $this->classWithStudents();
        $enrollment = $students[0]->enrollments()->firstOrFail();
        $grade = Grade::create([
            'enrollment_id' => $enrollment->id,
            'prelim' => 85,
            'midterm' => 86,
            'final' => 87,
            'final_grade' => 86,
            'remarks' => 'Passed',
            'status' => 'submitted',
            'encoded_by' => $faculty->id,
        ]);

        $this->actingAs($faculty)->put("/classes/{$class->id}/gradebook", [
            'grades' => [$enrollment->id => ['prelim' => '1.00', 'midterm' => '1.00', 'final' => '1.00']],
        ])->assertSessionHasErrors('grades');
        $this->assertEquals(86, $grade->fresh()->final_grade);

        $otherFaculty = User::factory()->create(['role' => 'instructor', 'status' => 'active']);
        $this->actingAs($otherFaculty)->get("/classes/{$class->id}")->assertForbidden();
        $this->actingAs($otherFaculty)->put("/classes/{$class->id}/gradebook", [
            'grades' => [$enrollment->id => ['prelim' => '2.00', 'midterm' => '2.00', 'final' => '2.00']],
        ])->assertForbidden();

        $past = SchoolYear::create(['school_year' => '2025-2026', 'semester' => '2nd Semester', 'status' => 'inactive']);
        $class->update(['school_year_id' => $past->id]);
        $this->actingAs($faculty)->put("/classes/{$class->id}/gradebook", [
            'grades' => [$enrollment->id => ['prelim' => '2.00', 'midterm' => '2.00', 'final' => '2.00']],
        ])->assertForbidden();
    }

    public function test_gradebook_rejects_enrollments_from_another_class(): void
    {
        [, $faculty, $class, $students] = $this->classWithStudents();
        $otherSubject = Subject::create(['subject_code' => 'ITE202', 'subject_name' => 'Networks', 'units' => 3, 'status' => 'active']);
        $otherClass = ClassAssignment::create([
            'instructor_id' => $class->instructor_id,
            'subject_id' => $otherSubject->id,
            'section_id' => $class->section_id,
            'school_year_id' => $class->school_year_id,
            'status' => 'active',
        ]);
        $foreignEnrollment = Enrollment::create([
            'student_id' => $students[0]->id,
            'class_assignment_id' => $otherClass->id,
            'status' => 'active',
        ]);

        $this->actingAs($faculty)->put("/classes/{$class->id}/gradebook", [
            'grades' => [$foreignEnrollment->id => ['prelim' => '1.50', 'midterm' => '1.50', 'final' => '1.50']],
        ])->assertSessionHasErrors('grades');
        $this->assertDatabaseMissing('grades', ['enrollment_id' => $foreignEnrollment->id]);
    }

    public function test_student_grades_list_subjects_in_order_and_hides_unreleased_scores(): void
    {
        [, $faculty, $class, $students] = $this->classWithStudents();
        $student = $students[0];
        $computingEnrollment = $student->enrollments()->firstOrFail();
        Grade::create([
            'enrollment_id' => $computingEnrollment->id,
            'prelim' => 84, 'midterm' => 84, 'final' => 84,
            'final_grade' => 84, 'remarks' => 'Passed',
            'status' => 'released', 'encoded_by' => $faculty->id,
        ]);

        $dataMining = Subject::create([
            'subject_code' => '26-01', 'subject_name' => 'DATA MINING', 'units' => 3, 'status' => 'active',
        ]);
        $miningClass = ClassAssignment::create([
            'instructor_id' => $class->instructor_id,
            'subject_id' => $dataMining->id,
            'section_id' => $class->section_id,
            'school_year_id' => $class->school_year_id,
            'status' => 'active',
        ]);
        $miningEnrollment = Enrollment::create([
            'student_id' => $student->id, 'class_assignment_id' => $miningClass->id, 'status' => 'active',
        ]);
        Grade::create([
            'enrollment_id' => $miningEnrollment->id,
            'prelim' => 97, 'midterm' => 97, 'final' => 97,
            'final_grade' => 97, 'remarks' => 'Passed',
            'status' => 'submitted', 'encoded_by' => $faculty->id,
        ]);

        $this->actingAs($student->user)->get('/grades')
            ->assertOk()
            ->assertSeeInOrder(['DATA MINING', '26-01', 'Computing', 'ITE101'])
            ->assertSee('84.00')
            ->assertSee('Awaiting release')
            ->assertDontSee('97.00')
            ->assertDontSee('>Apply<');
    }

    public function test_admin_grade_management_groups_records_by_instructor_and_class(): void
    {
        [$admin, $faculty, $class, $students] = $this->classWithStudents();
        Grade::create([
            'enrollment_id' => $students[0]->enrollments()->firstOrFail()->id,
            'prelim' => 88, 'midterm' => 90, 'final' => 91,
            'final_grade' => 89.67, 'remarks' => 'Passed',
            'status' => 'submitted', 'encoded_by' => $faculty->id,
        ]);

        $otherFacultyUser = User::factory()->create(['role' => 'instructor', 'status' => 'active']);
        $otherFaculty = Instructor::create([
            'user_id' => $otherFacultyUser->id, 'employee_number' => 'FAC-301',
            'first_name' => 'Teacher', 'last_name' => 'Other',
            'email' => $otherFacultyUser->email, 'status' => 'active',
        ]);
        $otherSubject = Subject::create([
            'subject_code' => 'ITE102', 'subject_name' => 'Programming', 'units' => 3, 'status' => 'active',
        ]);
        $otherClass = ClassAssignment::create([
            'instructor_id' => $otherFaculty->id,
            'subject_id' => $otherSubject->id,
            'section_id' => $class->section_id,
            'school_year_id' => $class->school_year_id,
            'status' => 'active',
        ]);
        $otherEnrollment = Enrollment::create([
            'student_id' => $students[1]->id, 'class_assignment_id' => $otherClass->id, 'status' => 'active',
        ]);
        Grade::create([
            'enrollment_id' => $otherEnrollment->id,
            'prelim' => 80, 'midterm' => 81, 'final' => 82,
            'final_grade' => 81, 'remarks' => 'Passed',
            'status' => 'released', 'encoded_by' => $otherFacultyUser->id,
        ]);

        $this->actingAs($admin)->get('/grades')
            ->assertOk()
            ->assertSeeInOrder(['Faculty, Test', 'ITE101 / Computing', 'Other, Teacher', 'ITE102 / Programming'])
            ->assertSee('Release');
        $this->actingAs($admin)->get('/grades?subject_id='.$class->subject_id)
            ->assertOk()
            ->assertSee('ITE101 / Computing')
            ->assertDontSee('ITE102 / Programming');
    }

    public function test_notification_dot_clears_when_viewed_and_returns_for_new_grades(): void
    {
        [$admin, $faculty, , $students] = $this->classWithStudents();
        $first = Grade::create([
            'enrollment_id' => $students[0]->enrollments()->firstOrFail()->id,
            'prelim' => 2.5, 'midterm' => 3, 'final' => 1.5,
            'final_grade' => 2.33, 'remarks' => 'Passed',
            'status' => 'submitted', 'encoded_by' => $faculty->id,
        ]);

        $this->actingAs($admin)->get('/dashboard')->assertSee('<span class="notification-dot"', false);
        $this->actingAs($admin)->post('/notifications/seen')->assertNoContent();
        $this->actingAs($admin)->get('/dashboard')->assertDontSee('<span class="notification-dot"', false);
        $this->assertNotNull($admin->fresh()->notifications_seen_at);

        $this->travel(1)->minutes();
        Grade::create([
            'enrollment_id' => $students[1]->enrollments()->firstOrFail()->id,
            'prelim' => 2, 'midterm' => 2, 'final' => 2,
            'final_grade' => 2, 'remarks' => 'Passed',
            'status' => 'submitted', 'encoded_by' => $faculty->id,
        ]);
        $this->actingAs($admin)->get('/dashboard')->assertSee('<span class="notification-dot"', false);
        $this->actingAs($admin)->get("/grades/{$first->id}")
            ->assertOk()
            ->assertDontSee('<th>Status</th>', false);
    }

    public function test_section_transfer_keeps_old_grade_history_and_new_classes_use_current_period(): void
    {
        [$admin, $faculty, $class, $students] = $this->classWithStudents();
        $student = $students[0];
        $oldEnrollment = $student->enrollments()->firstOrFail();
        Grade::create([
            'enrollment_id' => $oldEnrollment->id,
            'prelim' => 90,
            'midterm' => 90,
            'final' => 90,
            'final_grade' => 90,
            'remarks' => 'Passed',
            'status' => 'released',
            'encoded_by' => $faculty->id,
        ]);

        $newSection = Section::create(['section_name' => 'B', 'year_level' => '1st', 'program' => 'BSIT', 'status' => 'active']);
        $newSubject = Subject::create(['subject_code' => 'ITE102', 'subject_name' => 'Programming', 'units' => 3, 'status' => 'active']);
        $this->actingAs($admin)->post('/classes', [
            'instructor_id' => $class->instructor_id,
            'subject_id' => $newSubject->id,
            'section_id' => $newSection->id,
        ])->assertRedirect('/classes');

        $studentData = $student->only(['student_number', 'first_name', 'middle_name', 'last_name', 'email']);
        $this->actingAs($admin)->put("/students/{$student->id}", $studentData + ['section_id' => $newSection->id])
            ->assertRedirect('/students');

        $this->assertDatabaseHas('enrollments', ['id' => $oldEnrollment->id, 'status' => 'inactive']);
        $this->assertDatabaseHas('grades', ['enrollment_id' => $oldEnrollment->id, 'status' => 'released']);
        $newClass = ClassAssignment::where('section_id', $newSection->id)->firstOrFail();
        $this->assertDatabaseHas('enrollments', ['student_id' => $student->id, 'class_assignment_id' => $newClass->id, 'status' => 'active']);
        $this->assertSame(SchoolYear::periodFor(now())['school_year'], $newClass->schoolYear->school_year);
        $this->actingAs($student->user)->get('/grades')->assertSee('90.00');
    }

    private function classWithStudents(): array
    {
        $this->travelTo(now()->setDate(2026, 9, 28));

        $admin = User::factory()->create(['role' => 'administrator', 'status' => 'active']);
        $faculty = User::factory()->create(['role' => 'instructor', 'status' => 'active']);
        $instructor = Instructor::create([
            'user_id' => $faculty->id,
            'employee_number' => 'FAC-300',
            'first_name' => 'Test',
            'last_name' => 'Faculty',
            'email' => $faculty->email,
            'status' => 'active',
        ]);
        $section = Section::create(['section_name' => 'A', 'year_level' => '1st', 'program' => 'BSIT', 'status' => 'active']);
        $subject = Subject::create(['subject_code' => 'ITE101', 'subject_name' => 'Computing', 'units' => 3, 'status' => 'active']);
        $period = SchoolYear::activateCurrent(now());
        $class = ClassAssignment::create([
            'instructor_id' => $instructor->id,
            'subject_id' => $subject->id,
            'section_id' => $section->id,
            'school_year_id' => $period->id,
            'status' => 'active',
        ]);

        $students = collect();
        foreach ([1, 2] as $number) {
            $user = User::factory()->create(['role' => 'student', 'status' => 'active']);
            $student = Student::create([
                'user_id' => $user->id,
                'student_number' => "BCC-2026-30{$number}",
                'first_name' => "Learner{$number}",
                'last_name' => 'Test',
                'email' => $user->email,
                'section_id' => $section->id,
                'status' => 'active',
            ]);
            Enrollment::create(['student_id' => $student->id, 'class_assignment_id' => $class->id, 'status' => 'active']);
            $students->push($student);
        }

        return [$admin, $faculty, $class, $students];
    }
}


<?php

namespace Tests\Feature;

use App\Models\{ClassAssignment,Enrollment,Grade,Instructor,SchoolYear,Section,Student,Subject,User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DatabaseWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_adding_instructor_creates_a_login_account_and_shows_validation_errors(): void
    {
        $admin = User::factory()->create(['role' => 'administrator', 'status' => 'active']);
        $data = [
            'employee_number' => 'FAC-100',
            'first_name' => 'Maria',
            'middle_name' => '',
            'last_name' => 'Santos',
            'email' => 'maria.santos@example.test',
            'password' => 'SecurePass123',
        ];

        $this->actingAs($admin)->post('/instructors', $data)->assertRedirect('/instructors');

        $instructor = Instructor::where('employee_number', 'FAC-100')->firstOrFail();
        $this->assertSame('instructor', $instructor->user->role);
        $this->assertTrue(Hash::check('SecurePass123', $instructor->user->password));
        $this->assertDatabaseCount('instructors', 1);

        $this->actingAs($admin)->post('/instructors', $data)->assertSessionHasErrors(['employee_number', 'email']);
        $this->actingAs($admin)->post('/instructors', array_replace($data, [
            'employee_number' => 'FAC-101',
            'email' => 'maria2@example.test',
            'password' => 'short',
        ]))->assertSessionHasErrors('password');
        $this->assertDatabaseCount('instructors', 1);
    }

    public function test_adding_student_creates_account_and_class_enrollment(): void
    {
        [$admin, , $section, $assignment] = $this->academicSetup();

        $this->actingAs($admin)->post('/students', [
            'student_number' => 'BCC-2026-100', 'first_name' => 'Test', 'middle_name' => '',
            'last_name' => 'Student', 'email' => 'student100@example.test',
            'section_id' => $section->id,
        ])->assertRedirect('/students');

        $student = Student::where('student_number', 'BCC-2026-100')->firstOrFail();
        $this->assertTrue(Hash::check('BCC6100', $student->user->password));
        $this->assertDatabaseHas('users', ['email' => 'student100@example.test', 'role' => 'student', 'status' => 'active']);
        $this->assertDatabaseHas('enrollments', ['student_id' => $student->id, 'class_assignment_id' => $assignment->id, 'status' => 'active']);

        $this->actingAs($student->user)->put('/profile/password', [
            'current_password' => 'wrong-password', 'password' => 'NewStudent@123', 'password_confirmation' => 'NewStudent@123',
        ])->assertSessionHasErrors('current_password');
        $this->actingAs($student->user)->put('/profile/password', [
            'current_password' => 'BCC6100', 'password' => 'NewStudent@123', 'password_confirmation' => 'NewStudent@123',
        ])->assertRedirect();
        $this->assertTrue(Hash::check('NewStudent@123', $student->user->fresh()->password));

        $this->actingAs($admin)->patch("/students/{$student->id}/archive")->assertRedirect();
        $this->assertDatabaseHas('students', ['id' => $student->id, 'status' => 'archived']);
        $this->assertDatabaseHas('users', ['id' => $student->user_id, 'status' => 'inactive']);
        $this->assertDatabaseHas('enrollments', ['student_id' => $student->id, 'status' => 'inactive']);

        $this->actingAs($admin)->patch("/students/{$student->id}/restore")->assertRedirect();
        $this->assertDatabaseHas('enrollments', ['student_id' => $student->id, 'class_assignment_id' => $assignment->id, 'status' => 'active']);

        $student->user->update(['password' => Hash::make('StudentChangedIt')]);
        $this->actingAs($admin)->patch("/user-accounts/{$student->user_id}/reset-student-password")->assertRedirect();
        $this->assertTrue(Hash::check('BCC6100', $student->user->fresh()->password));
    }

    public function test_grade_workflow_and_role_visibility_are_enforced(): void
    {
        [$admin, $instructorUser, $section, $assignment] = $this->academicSetup();
        $studentUser = User::factory()->create(['role' => 'student', 'status' => 'active']);
        $student = Student::create(['user_id' => $studentUser->id, 'student_number' => 'BCC-2026-101', 'first_name' => 'Learner', 'last_name' => 'One', 'email' => $studentUser->email, 'section_id' => $section->id, 'status' => 'active']);
        $enrollment = Enrollment::create(['student_id' => $student->id, 'class_assignment_id' => $assignment->id, 'status' => 'active']);

        $this->actingAs($instructorUser)->post('/grades', ['enrollment_id' => $enrollment->id, 'prelim' => '1.00', 'midterm' => '1.25', 'final' => '1.50'])->assertRedirect("/classes/{$assignment->id}");
        $grade = Grade::firstOrFail();
        $this->assertSame('draft', $grade->status);
        $this->assertEquals(1.25, $grade->final_grade);

        $this->actingAs($instructorUser)->patch("/grades/{$grade->id}/submit")->assertRedirect();
        $this->actingAs($instructorUser)->get("/grades/{$grade->id}/edit")->assertForbidden();
        $this->actingAs($studentUser)->get("/grades/{$grade->id}")->assertForbidden();

        $this->actingAs($admin)->patch("/grades/{$grade->id}/release")->assertRedirect();
        $this->actingAs($studentUser)->get("/grades/{$grade->id}")->assertOk()->assertSee('1.25');
    }

    public function test_grade_entry_rejects_scores_outside_quarter_step_scale(): void
    {
        [, $instructorUser, $section, $assignment] = $this->academicSetup();
        $studentUser = User::factory()->create(['role' => 'student', 'status' => 'active']);
        $student = Student::create(['user_id' => $studentUser->id, 'student_number' => 'BCC-2026-102', 'first_name' => 'Learner', 'last_name' => 'Two', 'email' => $studentUser->email, 'section_id' => $section->id, 'status' => 'active']);
        $enrollment = Enrollment::create(['student_id' => $student->id, 'class_assignment_id' => $assignment->id, 'status' => 'active']);

        $this->actingAs($instructorUser)->post('/grades', [
            'enrollment_id' => $enrollment->id, 'prelim' => '1.10', 'midterm' => '90', 'final' => '5.25',
        ])->assertSessionHasErrors(['prelim', 'midterm', 'final']);
        $this->assertDatabaseCount('grades', 0);
    }

    private function academicSetup(): array
    {
        $admin = User::factory()->create(['role' => 'administrator', 'status' => 'active']);
        $instructorUser = User::factory()->create(['role' => 'instructor', 'status' => 'active']);
        $instructor = Instructor::create(['user_id' => $instructorUser->id, 'employee_number' => 'FAC-001', 'first_name' => 'Faculty', 'last_name' => 'Member', 'email' => $instructorUser->email, 'status' => 'active']);
        $section = Section::create(['section_name' => 'BSIT 1A', 'year_level' => '1st', 'program' => 'BSIT', 'status' => 'active']);
        $subject = Subject::create(['subject_code' => 'ITE101', 'subject_name' => 'Introduction to Computing', 'units' => 3, 'status' => 'active']);
        $year = SchoolYear::create(['school_year' => '2026-2027', 'semester' => '1st Semester', 'status' => 'active']);
        $assignment = ClassAssignment::create(['instructor_id' => $instructor->id, 'subject_id' => $subject->id, 'section_id' => $section->id, 'school_year_id' => $year->id, 'status' => 'active']);

        return [$admin, $instructorUser, $section, $assignment];
    }
}

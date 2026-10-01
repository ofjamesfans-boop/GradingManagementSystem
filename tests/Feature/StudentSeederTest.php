<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Database\Seeders\StudentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StudentSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_students_have_accounts_and_reseeding_preserves_changes(): void
    {
        $this->seed(StudentSeeder::class);
        $this->assertDatabaseCount('students', 20);
        $this->assertDatabaseCount('users', 20);
        foreach (Student::with('user')->get() as $student) {
            $this->assertSame('active', $student->status);
            $this->assertNull($student->section_id);
            $this->assertSame($student->email, $student->user->email);
            $this->assertSame('student', $student->user->role);
            $this->assertTrue(Hash::check(Student::defaultPasswordFor($student->student_number), $student->user->password));
        }

        $student = Student::where('student_number', '026-2001')->firstOrFail();
        $student->update(['first_name' => 'Edited', 'status' => 'archived']);
        $student->user->update(['password' => Hash::make('ChangedPassword123!'), 'status' => 'inactive']);
        $this->seed(StudentSeeder::class);
        $this->assertDatabaseCount('students', 20);
        $this->assertDatabaseCount('users', 20);
        $this->assertSame('Edited', $student->fresh()->first_name);
        $this->assertSame('archived', $student->fresh()->status);
        $this->assertSame('inactive', $student->user->fresh()->status);
        $this->assertTrue(Hash::check('ChangedPassword123!', $student->user->fresh()->password));
    }

    public function test_email_conflict_rolls_back_without_reusing_an_existing_account(): void
    {
        $user = User::factory()->create(['email' => 'student2002@gradeflow.test', 'role' => 'administrator']);
        try {
            $this->seed(StudentSeeder::class);
            $this->fail('Expected an email conflict.');
        } catch (\RuntimeException $exception) {
            $this->assertStringContainsString('already in use', $exception->getMessage());
        }
        $this->assertDatabaseCount('students', 0);
        $this->assertDatabaseCount('users', 1);
        $this->assertSame('administrator', $user->fresh()->role);
    }
}

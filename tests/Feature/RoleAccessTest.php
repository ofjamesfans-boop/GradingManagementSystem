<?php

namespace Tests\Feature;

use App\Models\{Student, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_management_pages(): void
    {
        $admin = User::factory()->create(['role' => 'administrator', 'status' => 'active']);

        $this->actingAs($admin)->get('/students')
            ->assertOk()
            ->assertSee('No students found. Add a student to get started.');
        $this->actingAs($admin)->get('/students/create')->assertOk()->assertSee('Add Student');
        $this->actingAs($admin)->get('/subjects/create')->assertOk()->assertSee('Add Subject');
        $this->actingAs($admin)->get('/classes/create')->assertOk()->assertSee('Assign Class');
        $this->actingAs($admin)->get('/instructors')->assertOk();
        $this->actingAs($admin)->get('/sections')->assertOk();
        $this->actingAs($admin)->get('/user-accounts')->assertOk();
        $this->actingAs($admin)->get('/reports')->assertOk()->assertSee('Download PDF');
        $pdf = $this->actingAs($admin)->get('/reports/pdf');
        $pdf->assertOk()->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $pdf->getContent());
    }

    public function test_non_admin_roles_cannot_open_admin_pages(): void
    {
        $instructor = User::factory()->create(['role' => 'instructor', 'status' => 'active']);
        $student = User::factory()->create(['role' => 'student', 'status' => 'active']);

        $this->actingAs($instructor)->get('/instructors')->assertForbidden();
        $this->actingAs($student)->get('/students')->assertForbidden();
        $this->actingAs($student)->get('/activity-logs')->assertForbidden();
        $this->actingAs($instructor)->get('/reports/pdf')->assertForbidden();
        $this->actingAs($student)->get('/reports/pdf')->assertForbidden();
        $this->actingAs(User::factory()->create(['role' => 'administrator', 'status' => 'active']))
            ->put('/profile/password', ['current_password' => 'password', 'password' => 'Changed@123', 'password_confirmation' => 'Changed@123'])
            ->assertForbidden();
    }

    public function test_each_role_can_render_its_own_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'administrator', 'status' => 'active']);
        $instructor = User::factory()->create(['role' => 'instructor', 'status' => 'active']);
        $student = User::factory()->create(['role' => 'student', 'status' => 'active']);

        $this->actingAs($admin)->get('/dashboard')->assertOk()->assertSee('Admin Dashboard');
        $this->actingAs($instructor)->get('/dashboard')->assertOk()->assertSee('Faculty workspace');
        $this->actingAs($student)->get('/dashboard')->assertOk()->assertSee('Student workspace');
    }

    public function test_deactivated_account_loses_access_on_its_next_request(): void
    {
        $student = User::factory()->create(['role' => 'student', 'status' => 'active']);
        $this->actingAs($student)->get('/dashboard')->assertOk();

        $student->update(['status' => 'inactive']);
        $this->get('/dashboard')->assertRedirect('/student/login');
        $this->assertGuest();
    }

    public function test_admin_cannot_deactivate_own_account(): void
    {
        $admin = User::factory()->create(['role' => 'administrator', 'status' => 'active']);

        $this->actingAs($admin)->put("/user-accounts/{$admin->id}", ['status' => 'inactive'])
            ->assertSessionHasErrors('status');
        $this->assertSame('active', $admin->fresh()->status);
    }

    public function test_archived_student_account_requires_record_restore_before_activation(): void
    {
        $admin = User::factory()->create(['role' => 'administrator', 'status' => 'active']);
        $account = User::factory()->create(['role' => 'student', 'status' => 'inactive']);
        Student::create([
            'user_id' => $account->id,
            'student_number' => '026-9001',
            'first_name' => 'Test',
            'last_name' => 'Student',
            'email' => $account->email,
            'status' => 'archived',
        ]);

        $this->actingAs($admin)->put("/user-accounts/{$account->id}", ['status' => 'active'])
            ->assertSessionHasErrors('status');
        $this->assertSame('inactive', $account->fresh()->status);
    }
}

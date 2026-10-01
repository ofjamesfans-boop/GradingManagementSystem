<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BootstrapAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_first_admin_requires_secrets_and_is_only_created_once(): void
    {
        putenv('ADMIN_EMAIL');
        putenv('ADMIN_PASSWORD');

        try {
            $this->artisan('gradeflow:bootstrap-admin')->assertFailed();
            $this->assertDatabaseCount('users', 0);

            putenv('ADMIN_EMAIL=owner@example.test');
            putenv('ADMIN_PASSWORD=UniqueSecret123!');

            $this->artisan('gradeflow:bootstrap-admin')->assertSuccessful();
            $admin = User::where('email', 'owner@example.test')->firstOrFail();
            $this->assertSame('administrator', $admin->role);
            $this->assertTrue(Hash::check('UniqueSecret123!', $admin->password));

            putenv('ADMIN_PASSWORD=AnotherSecret123!');
            $this->artisan('gradeflow:bootstrap-admin')->assertSuccessful();
            $this->assertDatabaseCount('users', 1);
            $this->assertTrue(Hash::check('UniqueSecret123!', $admin->fresh()->password));
        } finally {
            putenv('ADMIN_EMAIL');
            putenv('ADMIN_PASSWORD');
        }
    }
}

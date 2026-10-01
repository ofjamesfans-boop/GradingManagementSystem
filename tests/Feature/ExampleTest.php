<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_each_role_has_a_login_page(): void
    {
        $this->get('/')->assertRedirect('/admin/login');
        $this->get('/admin/login')->assertOk()->assertSee('Admin / Registrar Login');
        $this->get('/faculty/login')->assertOk()->assertSee('Faculty Login');
        $this->get('/student/login')->assertOk()->assertSee('Student Login');
    }
}

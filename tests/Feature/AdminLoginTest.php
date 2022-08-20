<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_login()
    {
        $admin = Admin::factory()->create();

        $response = $this->postJson('/admin/login', [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $this->assertAuthenticatedAs($admin, 'sanctum');
    }

    public function test_handle_wrong_credentials()
    {
        $response = $this->postJson('/admin/login', [
            'email' => fake()->email(),
            'password' => 'wrong_password',
        ]);

        $response->assertInvalid(['email']);

        $this->assertGuest();
    }

    public function test_admin_login_would_not_work_for_user_login()
    {
        $admin = Admin::factory()->create();

        $response = $this
            ->actingAs($admin)
            ->getJson('/api/user');

        $response->assertForbidden();
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/login', [
            'username' => $user->username,
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $this->assertAuthenticatedAs($user, 'sanctum');
    }

    public function test_handle_wrong_credentials()
    {
        $response = $this->postJson('/login', [
            'username' => fake()->userName(),
            'password' => 'wrong_password',
        ]);

        $response->assertInvalid(['username']);

        $this->assertGuest();
    }

    public function test_user_login_would_not_work_for_admin_login()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->getJson('/api/admin');

        $response->assertForbidden();
    }
}

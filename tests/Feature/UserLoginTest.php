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

    public function test_repeated_login_throw_error_message()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->postJson('/login', [
                'username' => $user->username,
                'password' => 'password',
            ]);

        $response->assertForbidden();

        $response->assertJson(['message' => "Already authenticated as {$user->username}, subsequent logins are not allowed."]);
    }

    public function test_user_can_logout()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user, 'web')
            ->postJson('/api/user/logout');

        $this->assertGuest('web');
    }
}

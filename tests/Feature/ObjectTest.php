<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Obj;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ObjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_index_objects()
    {
        $object = Obj::factory()->for(User::factory())->create();

        $response = $this->getJson('/api/objects');

        $response->assertJson([
            'data' => [
                $object->only([
                    'id',
                    'name',
                ]),
            ],
        ]);
    }

    public function test_admin_can_create_object()
    {
        $data = Obj::factory()->make()->toArray();

        $data['user'] = User::factory()
            ->make(['password' => Str::random()])
            ->only([
                'name',
                'email',
                'password',
            ]);

        $response = $this
            ->actingAs(Admin::factory()->create())
            ->postJson('/api/objects', $data);

        $response->assertStatus(201);
        $response->assertJson($data);

        $this->assertDatabaseHas('users', [
            'name' => $data['user']['name'],
            'email' => $data['user']['email'],
            // hashed password not easy to check, skip it
        ]);

        $this->assertDatabaseHas('objects', [
            'name' => $data['name'],
            'user_id' => $response['user']['id'],
        ]);
    }

    public function test_user_cannot_create_object()
    {
        $data = Obj::factory()->make()->toArray();

        $data['user'] = User::factory()
            ->make(['password' => Str::random()])
            ->only([
                'name',
                'email',
                'password',
            ]);

        $response = $this
            ->actingAs(User::factory()->create())
            ->postJson('/api/objects', $data);

        $response->assertForbidden();
    }

    public function test_guest_cannot_create_object()
    {
        $data = Obj::factory()->make()->toArray();

        $data['user'] = User::factory()
            ->make(['password' => Str::random()])
            ->only([
                'name',
                'email',
                'password',
            ]);

        $response = $this
            ->postJson('/api/objects', $data);

        $response->assertUnauthorized();
    }

    public function test_user_can_show_object()
    {
        $user = User::factory()->has(Obj::factory(), 'object')->create();

        $response = $this
            ->actingAs($user)
            ->getJson('/api/object');

        $response
            ->assertJson(
                $user->object->only([
                    'id',
                    'name',
                ])
            );
    }

    public function test_guest_cannot_show_object()
    {
        $response = $this->getJson('/api/object');

        $response->assertUnauthorized();
    }

    public function test_admin_cannot_show_object()
    {
        $response = $this
            ->actingAs(Admin::factory()->create())
            ->getJson('/api/object');

        $response->assertForbidden();
    }
}

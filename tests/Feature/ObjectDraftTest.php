<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Draft;
use App\Models\Obj;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ObjectDraftTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_store_drafts_for_object()
    {
        $user = User::factory()->create();

        $obj = Obj::factory()->for($user)->create();

        $data = Draft::factory()->make()->toArray();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->post("/api/objects/{$obj->id}/drafts", $data);

        $response->assertStatus(201);

        $response->assertJson($data);

        $this->assertTrue(
            DB::table('drafts')
                ->whereJsonContains('to', $data['to'])
                ->whereJsonContains('cc', $data['cc'])
                ->where('message', $data['message'])
                ->where('name', $data['name'])
                ->exists()
        );
    }

    public function test_only_name_is_required()
    {
        $user = User::factory()->create();

        $obj = Obj::factory()->for($user)->create();

        $data = Draft::factory()->make()->only(['name']);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->post("/api/objects/{$obj->id}/drafts", $data);

        $response->assertStatus(201);

        $response->assertJson($data);

        $this->assertTrue(
            DB::table('drafts')
                ->where('name', $data['name'])
                ->exists()
        );
    }

    public function test_guest_cannot_store_draft()
    {
        $obj = Obj::factory()->for(User::factory())->create();

        $data = Draft::factory()->make()->toArray();

        $response = $this
            ->postJson("/api/objects/{$obj->id}/drafts", $data);

        $response->assertUnauthorized();
    }

    public function test_user_cannot_store_drafts_for_other_object()
    {
        $obj = Obj::factory()->for(User::factory())->create();

        $data = Draft::factory()->make()->toArray();

        $response = $this
            ->actingAs(User::factory()->create())
            ->postJson("/api/objects/{$obj->id}/drafts", $data);

        $response->assertForbidden();
    }

    public function test_admin_can_store_drafts_for_any_object()
    {
        $obj = Obj::factory()->for(User::factory()->create())->create();

        $data = Draft::factory()->make()->toArray();

        $response = $this
            ->actingAs(Admin::factory()->create(), 'sanctum')
            ->post("/api/objects/{$obj->id}/drafts", $data);

        $response->assertStatus(201);

        $response->assertJson($data);

        $this->assertTrue(
            DB::table('drafts')
                ->whereJsonContains('to', $data['to'])
                ->whereJsonContains('cc', $data['cc'])
                ->where('message', $data['message'])
                ->where('name', $data['name'])
                ->exists()
        );
    }

}

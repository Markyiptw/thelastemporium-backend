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

    public function test_users_can_index_drafts_for_their_object()
    {
        $user = User::factory()->create();

        $object = Obj::factory()->for($user)->create();

        $draft = Draft::factory()->for($object, 'object')->create();

        $response = $this
            ->actingAs($user)
            ->get("/api/objects/{$object->id}/drafts");

        $response->assertStatus(200);

        $response->assertJson([
            'data' => [
                $draft->only([
                    'to',
                    'cc',
                    'message',
                    'id',
                    'name',
                ]),
            ],
        ]);
    }

    public function test_drafts_for_other_object_are_not_indexed()
    {
        $user = User::factory()->create();

        $object = Obj::factory()->for($user)->create();

        Draft::factory()
            ->for(Obj::factory()->for(User::factory()), 'object')
            ->create();

        $response = $this
            ->actingAs($user)
            ->get("/api/objects/{$object->id}/drafts");

        $response->assertStatus(200);

        $this->assertEmpty($response['data']);
    }

    public function test_guest_cannot_index_drafts()
    {
        $user = User::factory()->create();

        $object = Obj::factory()->for($user)->create();

        Draft::factory()->for($object, 'object')->create();

        $response = $this
            ->getJson("/api/objects/{$object->id}/drafts");

        $response->assertUnauthorized();
    }

    public function test_user_cannot_index_drafts_for_object_not_belongs_to_them()
    {
        $object = Obj::factory()->for(User::factory())->create();

        $response = $this
            ->actingAs(User::factory()->create())
            ->get("/api/objects/{$object->id}/drafts");

        $response->assertForbidden();
    }

    public function test_admin_can_index_drafts_any_object()
    {

        $object = Obj::factory()->for(User::factory()->create())->create();

        $draft = Draft::factory()->for($object, 'object')->create();

        $response = $this
            ->actingAs(Admin::factory()->create())
            ->get("/api/objects/{$object->id}/drafts");

        $response->assertStatus(200);

        $response->assertJson([
            'data' => [
                $draft->only([
                    'to',
                    'cc',
                    'message',
                    'id',
                    'name',
                ]),
            ],
        ]);
    }

    public function test_users_can_list_draft_for_their_object()
    {
        $user = User::factory()->create();
        $object = Obj::factory()->for($user)->create();
        $draft = Draft::factory()->for($object, 'object')->create();

        $response = $this
            ->actingAs($user)
            ->get("/api/objects/{$object->id}/drafts/{$draft->id}");

        $response->assertStatus(200);

        $response->assertJson(
            $draft->only([
                'to',
                'cc',
                'message',
                'id',
                'name',
            ]),
        );
    }

    public function test_mail_not_belongs_to_object_return_not_found()
    {
        $object = Obj::factory()->for(User::factory())->create();
        $draft = Draft::factory()->for(Obj::factory()->for(User::factory()), 'object')->create();

        $response = $this
            ->actingAs(Admin::factory()->create())
            ->get("/api/objects/{$object->id}/drafts/{$draft->id}");

        $response->assertNotFound();
    }

    public function test_guest_cannot_show_draft()
    {
        $object = Obj::factory()->for(User::factory())->create();
        $draft = Draft::factory()->for($object, 'object')->create();

        $response = $this
            ->getJson("/api/objects/{$object->id}/drafts/{$draft->id}");

        $response->assertUnauthorized();
    }

    public function test_admin_can_show_any_draft()
    {
        $object = Obj::factory()->for(User::factory())->create();
        $draft = Draft::factory()->for($object, 'object')->create();

        $response = $this
            ->actingAs(Admin::factory()->create())
            ->getJson("/api/objects/{$object->id}/drafts/{$draft->id}");

        $response->assertStatus(200);

        $response->assertJson(
            $draft->only([
                'to',
                'cc',
                'message',
                'id',
                'name',
            ]),
        );
    }

    public function test_user_cannot_show_draft_not_belongs_to_them()
    {
        $object = Obj::factory()->for(User::factory())->create();
        $draft = Draft::factory()->for($object, 'object')->create();

        $response = $this
            ->actingAs(User::factory()->create())
            ->getJson("/api/objects/{$object->id}/drafts/{$draft->id}");

        $response->assertForbidden();
    }

}
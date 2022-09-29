<?php

namespace Tests\Feature;

use App\Mail\MessageFromTheLastEmporium;
use App\Models;
use App\Models\Admin;
use App\Models\Draft;
use App\Models\Obj;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
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
                ->where('from', $data['from'])
                ->whereJsonContains('to', $data['to'])
                ->whereJsonContains('cc', $data['cc'])
                ->where('message', $data['message'])
                ->where('location', $data['location'])
                ->exists()
        );
    }

    public function test_to_is_optional()
    {
        $user = User::factory()->create();

        $obj = Obj::factory()->for($user)->create();

        $data = collect(Draft::factory()->make()->toArray())->except(['to'])->all();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->post("/api/objects/{$obj->id}/drafts", $data);

        $response->assertStatus(201);

        $response->assertJson($data);

        $this->assertTrue(
            DB::table('drafts')
                ->where('from', $data['from'])
                ->whereNull('to')
                ->whereJsonContains('cc', $data['cc'])
                ->where('message', $data['message'])
                ->where('location', $data['location'])
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
                ->where('from', $data['from'])
                ->whereJsonContains('to', $data['to'])
                ->whereJsonContains('cc', $data['cc'])
                ->where('message', $data['message'])
                ->where('location', $data['location'])
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
                collect($draft)
                    ->except([
                        'created_at',
                        'updated_at',
                        'object_id',
                    ])
                    ->all(),
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
                collect($draft)
                    ->except([
                        'created_at',
                        'updated_at',
                        'object_id',
                    ])
                    ->all(),
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
            collect($draft)
                ->except([
                    'created_at',
                    'updated_at',
                    'object_id',
                ])
                ->all(),
        );
    }

    public function test_draft_not_belongs_to_object_return_not_found()
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
            collect($draft)
                ->except([
                    'created_at',
                    'updated_at',
                    'object_id',
                ])
                ->all(),
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

    public function test_users_can_edit_draft_for_their_object()
    {
        $user = User::factory()->create();

        $obj = Obj::factory()->for($user)->create();

        $draft = Draft::factory()
            ->for($obj, 'object')
            ->create();

        $data = Draft::factory()->make()->toArray();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->patch("/api/objects/{$obj->id}/drafts/{$draft->id}", $data);

        $response->assertStatus(200);

        $response->assertJson(
            array_merge($data, ['id' => $draft->id])
        );

        $this->assertTrue(
            DB::table('drafts')
                ->where('from', $data['from'])
                ->whereJsonContains('to', $data['to'])
                ->whereJsonContains('cc', $data['cc'])
                ->where('message', $data['message'])
                ->where('location', $data['location'])
                ->exists()
        );
    }

    public function test_editing_draft_not_belongs_to_object_return_not_found()
    {
        $object = Obj::factory()->for(User::factory())->create();
        $draft = Draft::factory()->for(Obj::factory()->for(User::factory()), 'object')->create();

        $response = $this
            ->actingAs(Admin::factory()->create())
            ->patchJson("/api/objects/{$object->id}/drafts/{$draft->id}");

        $response->assertNotFound();
    }

    public function test_guests_cannot_edit_draft()
    {
        $obj = Obj::factory()->for(User::factory()->create())->create();

        $draft = Draft::factory()
            ->for($obj, 'object')
            ->create();

        $data = Draft::factory()->make()->toArray();

        $response = $this
            ->patchJson("/api/objects/{$obj->id}/drafts/{$draft->id}", $data);

        $response->assertUnauthorized();
    }

    public function test_admins_can_edit_any_draft()
    {
        $obj = Obj::factory()->for(User::factory()->create())->create();

        $draft = Draft::factory()
            ->for($obj, 'object')
            ->create();

        $data = Draft::factory()->make()->toArray();

        $response = $this
            ->actingAs(Admin::factory()->create(), 'sanctum')
            ->patch("/api/objects/{$obj->id}/drafts/{$draft->id}", $data);

        $response->assertStatus(200);

        $response->assertJson(
            array_merge($data, ['id' => $draft->id])
        );

        $this->assertTrue(
            DB::table('drafts')
                ->where('from', $data['from'])
                ->whereJsonContains('to', $data['to'])
                ->whereJsonContains('cc', $data['cc'])
                ->where('message', $data['message'])
                ->where('location', $data['location'])
                ->exists()
        );
    }

    public function test_user_cannot_edit_drafts_not_belongs_to_them()
    {
        $obj = Obj::factory()->for(User::factory()->create())->create();

        $draft = Draft::factory()
            ->for($obj, 'object')
            ->create();

        $data = Draft::factory()->make()->toArray();

        $response = $this
            ->actingAs(User::factory()->create())
            ->patchJson("/api/objects/{$obj->id}/drafts/{$draft->id}", $data);

        $response->assertForbidden();
    }

    public function test_user_can_send_draft()
    {
        $user = User::factory()->create();

        $obj = Obj::factory()->for($user)->create();

        $draft = Draft::factory()
            ->for($obj, 'object')
            ->create();

        Mail::fake();

        $data = Models\Mail::factory()->make()->toArray(); // use fake mail insdead of fake draft

        $response = $this
            ->actingAs($user, 'sanctum')
            ->postJson("/api/objects/{$obj->id}/drafts/{$draft->id}/send", $data);

        $response->assertStatus(201);

        $response->assertJson($data);

        Mail::assertSent(function (MessageFromTheLastEmporium $mail) use ($data) {
            return $mail->message = $data['message'] &&
            $mail->hasTo($data['to']) &&
            $mail->hasCc($data['cc']) &&
            $mail->hasSubject($data['subject'])
            ;
        });

        $this->assertTrue(
            DB::table('mails')
                ->whereJsonContains('to', $data['to'])
                ->whereJsonContains('cc', $data['cc'])
                ->where('subject', $data['subject'])
                ->where('message', $data['message'])
                ->exists()
        );

        $this->assertModelMissing($draft);
    }

    //

    public function test_sending_draft_not_belongs_to_object_return_not_found()
    {
        $object = Obj::factory()->for(User::factory())->create();
        $draft = Draft::factory()->for(Obj::factory()->for(User::factory()), 'object')->create();

        $response = $this
            ->actingAs(Admin::factory()->create())
            ->postJson("/api/objects/{$object->id}/drafts/{$draft->id}/send", Models\Mail::factory()->make()->toArray());

        $response->assertNotFound();
    }

    public function test_guests_cannot_send_draft()
    {
        $obj = Obj::factory()->for(User::factory()->create())->create();

        $draft = Draft::factory()
            ->for($obj, 'object')
            ->create();

        $data = Models\Mail::factory()->make()->toArray();

        $response = $this
            ->postJson("/api/objects/{$obj->id}/drafts/{$draft->id}/send", $data);

        $response->assertUnauthorized();
    }

    public function test_admins_can_send_any_draft()
    {
        $obj = Obj::factory()->for(User::factory()->create())->create();

        $draft = Draft::factory()
            ->for($obj, 'object')
            ->create();

        Mail::fake();

        $data = Models\Mail::factory()->make()->toArray(); // use fake mail insdead of fake draft

        $response = $this
            ->actingAs(Admin::factory()->create(), 'sanctum')
            ->postJson("/api/objects/{$obj->id}/drafts/{$draft->id}/send", $data);

        $response->assertStatus(201);

        $response->assertJson($data);

        Mail::assertSent(function (MessageFromTheLastEmporium $mail) use ($data) {
            return $mail->message = $data['message'] &&
            $mail->hasTo($data['to']) &&
            $mail->hasCc($data['cc']) &&
            $mail->hasSubject($data['subject'])
            ;
        });

        $this->assertTrue(
            DB::table('mails')
                ->whereJsonContains('to', $data['to'])
                ->whereJsonContains('cc', $data['cc'])
                ->where('subject', $data['subject'])
                ->where('message', $data['message'])
                ->where('location', $data['location'])
                ->exists()
        );

        $this->assertModelMissing($draft);
    }

    public function test_user_cannot_send_drafts_not_belongs_to_them()
    {
        $obj = Obj::factory()->for(User::factory()->create())->create();

        $draft = Draft::factory()
            ->for($obj, 'object')
            ->create();

        $data = Models\Mail::factory()->make()->toArray(); // use fake mail insdead of fake draft

        $response = $this
            ->actingAs(User::factory()->create())
            ->postJson("/api/objects/{$obj->id}/drafts/{$draft->id}/send", $data);

        $response->assertForbidden();
    }

    public function test_validation_rules_from_mail_sending_applies()
    {
        $obj = Obj::factory()->for(User::factory())->create();

        $draft = Draft::factory()
            ->for($obj, 'object')
            ->create();

        Mail::fake();

        $response = $this
            ->actingAs(Admin::factory()->create())
            ->postJson("/api/objects/{$obj->id}/drafts/{$draft->id}/send");

        $response->assertInvalid(['to', 'subject', 'message']);
    }

    public function test_admin_can_delete_drafts()
    {
        $obj = Obj::factory()->for(User::factory())->create();

        $draft = Draft::factory()
            ->for($obj, 'object')
            ->create();

        $response = $this
            ->actingAs(Admin::factory()->create())
            ->deleteJson("/api/objects/{$obj->id}/drafts/{$draft->id}");

        $response->assertStatus(204);

        $this->assertModelMissing($draft);
    }

    public function test_user_cannot_delete_their_own_draft()
    {
        $user = User::factory()->create();

        $obj = Obj::factory()->for(User::factory())->create();

        $draft = Draft::factory()
            ->for($obj, 'object')
            ->create();

        $response = $this
            ->actingAs($user)
            ->deleteJson("/api/objects/{$obj->id}/drafts/{$draft->id}");

        $response->assertForbidden();
    }

    public function test_deleting_draft_not_belongs_to_object_return_not_found()
    {
        $object = Obj::factory()->for(User::factory())->create();
        $draft = Draft::factory()->for(Obj::factory()->for(User::factory()), 'object')->create();

        $response = $this
            ->actingAs(Admin::factory()->create())
            ->deleteJson("/api/objects/{$object->id}/drafts/{$draft->id}");

        $response->assertNotFound();
    }

    public function test_guests_cannot_delete_draft()
    {
        $obj = Obj::factory()->for(User::factory())->create();

        $draft = Draft::factory()
            ->for($obj, 'object')
            ->create();

        $response = $this
            ->deleteJson("/api/objects/{$obj->id}/drafts/{$draft->id}");

        $response->assertUnauthorized();
    }

    public function test_user_cannot_delete_drafts_not_belongs_to_them()
    {
        $obj = Obj::factory()->for(User::factory()->create())->create();

        $draft = Draft::factory()
            ->for($obj, 'object')
            ->create();

        $response = $this
            ->actingAs(User::factory()->create())
            ->deleteJson("/api/objects/{$obj->id}/drafts/{$draft->id}");

        $response->assertForbidden();
    }
}

<?php

namespace Tests\Feature;

use App\Mail\MessageFromTheLastEmporium;
use App\Models;
use App\Models\Admin;
use App\Models\Obj;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ObjectMailTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_send_mail_for_object()
    {
        $user = User::factory()->create();

        $obj = Obj::factory()->for($user)->create();

        Mail::fake();

        $data = Models\Mail::factory()->make()->toArray();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->post("/api/objects/{$obj->id}/mails", $data);

        $response->assertStatus(201);

        $response->assertJson($data);

        Mail::assertSent(function (MessageFromTheLastEmporium $mail) use ($data) {
            return $mail->message = $data['message'] &&
            $mail->hasTo($data['to']) &&
            $mail->hasCc($data['cc']);
        });

        $this->assertTrue(
            DB::table('mails')
                ->where('from', $data['from'])
                ->whereJsonContains('to', $data['to'])
                ->whereJsonContains('cc', $data['cc'])
                ->where('message', $data['message'])
                ->where('location', $data['location'])
                ->exists()
        );
    }

    public function test_user_can_send_mail_for_object_without_cc()
    {
        $user = User::factory()->create();

        $obj = Obj::factory()->for($user)->create();

        Mail::fake();

        $data = collect(Models\Mail::factory()->make())->except(['cc'])->all();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->postJson("/api/objects/{$obj->id}/mails", $data);

        $response->assertStatus(201);

        $response->assertJson($data);

        Mail::assertSent(function (MessageFromTheLastEmporium $mail) use ($data) {
            return $mail->message = $data['message'] &&
            $mail->hasTo($data['to'])
            ;
        });

        $this->assertTrue(
            DB::table('mails')
                ->where('from', $data['from'])
                ->whereJsonContains('to', $data['to'])
                ->whereNull('cc')
                ->where('message', $data['message'])
                ->where('location', $data['location'])
                ->exists()
        );
    }

    public function test_user_can_send_mail_for_object_with_empty_cc_array()
    {
        $user = User::factory()->create();

        $obj = Obj::factory()->for($user)->create();

        Mail::fake();

        $data = Models\Mail::factory()->make(['cc' => []])->toArray();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->post("/api/objects/{$obj->id}/mails", $data);

        $response->assertStatus(201);

        $response->assertJson($data);

        Mail::assertSent(function (MessageFromTheLastEmporium $mail) use ($data) {
            return $mail->message = $data['message'] &&
            $mail->hasTo($data['to']);
        });

        $this->assertTrue(
            DB::table('mails')
                ->where('from', $data['from'])
                ->whereJsonContains('to', $data['to'])
                ->whereJsonContains('cc', $data['cc'])
                ->where('message', $data['message'])
                ->where('location', $data['location'])
                ->exists()
        );
    }

    public function test_guest_cannot_send_mail()
    {
        $obj = Obj::factory()->for(User::factory())->create();

        Mail::fake();

        $data = Models\Mail::factory()->make()->toArray();

        $response = $this
            ->postJson("/api/objects/{$obj->id}/mails", $data);

        $response->assertUnauthorized();
    }

    public function test_user_cannot_send_mail_for_other_object()
    {
        $obj = Obj::factory()->for(User::factory())->create();

        Mail::fake();

        $data = Models\Mail::factory()->make()->toArray();

        $response = $this
            ->actingAs(User::factory()->create())
            ->postJson("/api/objects/{$obj->id}/mails", $data);

        $response->assertForbidden();
    }

    public function test_admin_can_send_mail_for_any_object()
    {
        $obj = Obj::factory()->for(User::factory())->create();

        Mail::fake();

        $data = Models\Mail::factory()->make()->toArray();

        $response = $this
            ->actingAs(Admin::factory()->create())
            ->postJson("/api/objects/{$obj->id}/mails", $data);

        Mail::assertSent(function (MessageFromTheLastEmporium $mail) use ($data) {
            return $mail->message = $data['message'] &&
            $mail->hasTo($data['to']) &&
            $mail->hasCc($data['cc']);
        });

        $response->assertStatus(201);

        $response->assertJson($data);

        $this->assertTrue(
            DB::table('mails')
                ->where('from', $data['from'])
                ->whereJsonContains('to', $data['to'])
                ->whereJsonContains('cc', $data['cc'])
                ->where('message', $data['message'])
                ->where('location', $data['location'])
                ->exists()
        );
    }

    public function test_users_can_index_mails_for_their_object()
    {
        $user = User::factory()->create();

        $object = Obj::factory()->for($user)->create();

        $mail = Models\Mail::factory()->for($object, 'object')->create();

        $response = $this
            ->actingAs($user)
            ->get("/api/objects/{$object->id}/mails");

        $response->assertStatus(200);

        $response->assertJson([
            'data' => [
                $mail->only([
                    'from',
                    'to',
                    'cc',
                    'message',
                    'location',
                ]),
            ],
        ]);
    }

    public function test_mails_for_other_object_are_not_indexed()
    {
        $user = User::factory()->create();

        $object = Obj::factory()->for($user)->create();

        Models\Mail::factory()
            ->for(Obj::factory()->for(User::factory()), 'object')
            ->create();

        $response = $this
            ->actingAs($user)
            ->get("/api/objects/{$object->id}/mails");

        $response->assertStatus(200);

        $this->assertEmpty($response['data']);
    }

    public function test_guest_cannot_index_mails()
    {
        $user = User::factory()->create();

        $object = Obj::factory()->for($user)->create();

        Models\Mail::factory()->for($object, 'object')->create();

        $response = $this
            ->getJson("/api/objects/{$object->id}/mails");

        $response->assertUnauthorized();
    }

    public function test_user_cannot_index_mails_for_object_not_belongs_to_them()
    {
        $object = Obj::factory()->for(User::factory())->create();

        $response = $this
            ->actingAs(User::factory()->create())
            ->get("/api/objects/{$object->id}/mails");

        $response->assertForbidden();
    }

    public function test_admin_can_index_mails_for_any_object()
    {
        $object = Obj::factory()->for(User::factory())->create();

        $mail = Models\Mail::factory()->for($object, 'object')->create();

        $response = $this
            ->actingAs(Admin::factory()->create())
            ->get("/api/objects/{$object->id}/mails");

        $response->assertStatus(200);

        $response->assertJson([
            'data' => [
                $mail->only([
                    'from',
                    'to',
                    'cc',
                    'message',
                    'location',
                ]),
            ],
        ]);
    }

    public function test_users_can_show_mail_for_their_object()
    {
        $user = User::factory()->create();
        $object = Obj::factory()->for($user)->create();
        $mail = Models\Mail::factory()->for($object, 'object')->create();

        $response = $this
            ->actingAs($user)
            ->get("/api/objects/{$object->id}/mails/{$mail->id}");

        $response->assertStatus(200);

        $response->assertJson(
            $mail->only([
                'from',
                'to',
                'cc',
                'message',
                'location',
            ]),
        );
    }

    public function test_mail_not_belongs_to_object_return_not_found()
    {
        $object = Obj::factory()->for(User::factory())->create();
        $mail = Models\Mail::factory()->for(Obj::factory()->for(User::factory()), 'object')->create();

        $response = $this
            ->actingAs(Admin::factory()->create())
            ->get("/api/objects/{$object->id}/mails/{$mail->id}");

        $response->assertNotFound();
    }

    public function test_guest_cannot_show_mail()
    {
        $object = Obj::factory()->for(User::factory())->create();
        $mail = Models\Mail::factory()->for($object, 'object')->create();

        $response = $this
            ->getJson("/api/objects/{$object->id}/mails/{$mail->id}");

        $response->assertUnauthorized();
    }

    public function test_admin_can_show_any_mail()
    {
        $object = Obj::factory()->for(User::factory())->create();
        $mail = Models\Mail::factory()->for($object, 'object')->create();

        $response = $this
            ->actingAs(Admin::factory()->create())
            ->getJson("/api/objects/{$object->id}/mails/{$mail->id}");

        $response->assertStatus(200);

        $response->assertJson(
            $mail->only([
                'from',
                'to',
                'cc',
                'message',
                'location',
            ]),
        );
    }

    public function test_user_cannot_show_mail_not_belongs_to_them()
    {
        $object = Obj::factory()->for(User::factory())->create();
        $mail = Models\Mail::factory()->for($object, 'object')->create();

        $response = $this
            ->actingAs(User::factory()->create())
            ->getJson("/api/objects/{$object->id}/mails/{$mail->id}");

        $response->assertForbidden();
    }
}

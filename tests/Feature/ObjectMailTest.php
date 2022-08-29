<?php

namespace Tests\Feature;

use App\Mail\MessageFromTheLastEmporium;
use App\Models;
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
            return $mail->message = $data['message'];
        });

        $this->assertTrue(
            DB::table('mails')
                ->whereJsonContains('to', $data['to'])
                ->whereJsonContains('cc', $data['cc'])
                ->where('message', $data['message'])
                ->exists()
        );
    }
}

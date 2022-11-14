<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Mail;
use App\Models\Obj;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MailTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_mail()
    {
        $mail = Mail::factory()
            ->for(Obj::factory()
                ->for(User::factory()), 'object')
            ->create();

        $response = $this
            ->actingAs(Admin::factory()->create())
            ->deleteJson("/api/mails/{$mail->id}");

        $response->assertSuccessful();

        $this->assertModelMissing($mail);
    }
}

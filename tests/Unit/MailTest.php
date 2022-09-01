<?php

namespace Tests\Unit;

use App\Models;
use App\Models\Obj;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MailTest extends TestCase
{
    use RefreshDatabase;

    public function test_mail_has_object()
    {
        $obj = Obj::factory()->for(User::factory())->create();

        $mail = Models\Mail::factory()->create(['object_id' => $obj->id]);

        $this->assertTrue($mail->object->is($obj));
    }
}

<?php

namespace Tests\Unit;

use App\Models\Obj;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_object()
    {
        $user = User::factory()->create();

        $obj = Obj::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->refresh()->object->is($obj));
    }
}

<?php

namespace Tests\Unit;

use App\Models\Obj;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ObjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_object_has_user()
    {
        $user = User::factory()->create();

        $obj = Obj::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($obj->user->is($user));
    }
}

<?php

namespace Tests\Feature;

use App\Models\Obj;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}

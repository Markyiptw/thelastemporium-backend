<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Obj;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ObjectLocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_can_index_locations_of_an_object()
    {
        $obj = Obj::factory()->create();

        $location = Location::factory()
            ->for($obj, 'object')
            ->create();

        $response = $this->getJson("/api/objects/{$obj->id}/locations");

        $response->assertJson([
            'data' => [
                $location->only([
                    'latitude',
                    'longitude',
                ]),
            ],
        ]);
    }

    public function test_guests_other_locations_are_not_returned_for_an_object()
    {
        $objs = Obj::factory()->count(2)->create();

        Location::factory()
            ->for($objs[0], 'object') // create location for object 0
            ->create();

        $response = $this->getJson("/api/objects/{$objs[1]->id}/locations"); // but index object 1

        $this->assertEmpty($response['data']);
    }
}

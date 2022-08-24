<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Location;
use App\Models\Obj;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ObjectLocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_can_index_locations_of_an_object()
    {
        $obj = Obj::factory()->for(User::factory())->create();

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
        $objs = [];

        for ($i = 0; $i < 2; $i++) {
            $objs[] = Obj::factory()->for(User::factory())->create();
        }

        Location::factory()
            ->for($objs[0], 'object') // create location for object 0
            ->create();

        $response = $this->getJson("/api/objects/{$objs[1]->id}/locations"); // but index object 1

        $this->assertEmpty($response['data']);
    }

    public function test_guest_cannot_store_locations()
    {
        $user = User::factory()->create();

        $obj = Obj::factory()->for($user)->create();

        $locationData = Location::factory()->make()->toArray();

        $response = $this->postJson("/api/objects/{$obj->id}/locations", $locationData);

        $response->assertUnauthorized();
    }

    public function test_user_can_store_locations_for_their_object()
    {
        $user = User::factory()->create();

        $obj = Obj::factory()->for($user)->create();

        $locationData = Location::factory()->make()->toArray();

        $response = $this
            ->actingAs($user)
            ->postJson("/api/objects/{$obj->id}/locations", $locationData);

        $response->assertStatus(201);
        $response->assertJson($locationData);
    }

    public function test_user_cannot_store_locations_for_other_object()
    {
        $users = User::factory()->count(2)->has(Obj::factory(), 'object')->create();

        $locationData = Location::factory()->make()->toArray();

        $response = $this
            ->actingAs($users[0])
            ->postJson("/api/objects/{$users[1]->object->id}/locations", $locationData);

        $response->assertForbidden();
    }

    public function test_admin_can_store_locations_for_any_object()
    {
        $obj = Obj::factory()->for(User::factory())->create();

        $locationData = Location::factory()->make()->toArray();

        $response = $this
            ->actingAs(Admin::factory()->create())
            ->postJson("/api/objects/{$obj->id}/locations", $locationData);

        $response->assertStatus(201);
        $response->assertJson($locationData);
    }

    //validation not tested but implemented
}

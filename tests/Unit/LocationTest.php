<?php

namespace Tests\Unit;

use App\Models\Location;
use App\Models\Obj;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_location_belongs_to_an_object()
    {
        $obj = Obj::factory()->create();

        $location = Location::factory()->create(['object_id' => $obj->id]);

        $this->assertTrue($location->object->is($obj));
    }
}

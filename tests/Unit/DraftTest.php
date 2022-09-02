<?php

namespace Tests\Unit;

use App\Models\Draft;
use App\Models\Obj;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DraftTest extends TestCase
{
    use RefreshDatabase;

    public function test_draft_has_object()
    {
        $obj = Obj::factory()->for(User::factory())->create();

        $draft = Draft::factory()->create(['object_id' => $obj->id]);

        $this->assertTrue($draft->object->is($obj));
    }
}

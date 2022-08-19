<?php

namespace Tests\Unit;

use App\Models\Media;
use App\Models\Obj;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ObjTest extends TestCase
{
    use RefreshDatabase;

    public function test_obj_has_medias()
    {
        $obj = Obj::factory()->for(User::factory())->create();

        $media = Media::factory()->create(['object_id' => $obj->id]);

        $this->assertTrue($obj->medias->contains($media));
    }
}

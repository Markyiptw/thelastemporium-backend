<?php

namespace Tests\Unit;

use App\Models\Media;
use App\Models\Obj;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ObjTest extends TestCase
{
    use RefreshDatabase;

    public function test_obj_has_medias()
    {
        $obj = Obj::factory()->create();

        $media = Media::factory()->create(['object_id' => $obj->id]);

        $this->assertTrue($obj->medias->contains($media));
    }
}

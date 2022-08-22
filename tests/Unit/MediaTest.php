<?php

namespace Tests\Unit;

use App\Models\Media;
use App\Models\Obj;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaTest extends TestCase
{
    use RefreshDatabase;

    public function test_media_has_object()
    {
        $obj = Obj::factory()->for(User::factory())->create();

        Storage::fake('public');
        $media = Media::factory()->create(['object_id' => $obj->id]);

        $this->assertTrue($media->object->is($obj));
    }
}

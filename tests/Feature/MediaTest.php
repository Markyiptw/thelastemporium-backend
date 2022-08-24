<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\Obj;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_index_medias()
    {
        Storage::fake('public');

        $media = Media::factory()
            ->for(
                Obj::factory()
                    ->for(User::factory()), 'object'
            )
            ->create();

        $response = $this->get("/api/medias");

        $response->assertStatus(200);
        $response->assertJson(
            ['data' => [$media->only([
                'id',
                'path',
                'mime_type',
            ])]]
        );
    }
}

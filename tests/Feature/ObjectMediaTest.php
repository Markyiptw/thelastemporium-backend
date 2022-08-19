<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\Obj;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ObjectMediaTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_upload_m4a_to_location()
    {
        $user = User::factory()->create();

        $object = Obj::factory()->for($user)->create();

        Storage::fake('public');

        $file = UploadedFile::fake()->create('foo.m4a', 0, 'audio/x-m4a'); // https://stackoverflow.com/questions/65796709/what-content-type-are-voice-memo-files-on-ios

        $response = $this
            ->actingAs($user)
            ->postJson("/api/objects/{$object->id}/medias", [
                'file' => $file,
            ]);

        $response->assertStatus(201);

        $response->assertJson(
            Media::first()->only([
                'path',
                'mime_type',
                'id',
            ])
        );

        Storage::disk('public')->assertExists($file->hashName());

        $this->assertDatabaseHas('medias', [
            'path' => $file->hashName(), // because stored in root of disk
            'mime_type' => 'audio/x-m4a',
            'object_id' => $object->id,
        ]);
    }

    public function test_user_can_upload_jpg_to_location()
    {
        $user = User::factory()->create();

        $object = Obj::factory()->for($user)->create();

        Storage::fake('public');

        $file = UploadedFile::fake()->image('foo.jpg');

        $response = $this
            ->actingAs($user)
            ->postJson("/api/objects/{$object->id}/medias", [
                'file' => $file,
            ]);

        $response->assertStatus(201);

        $response->assertJson(
            Media::first()->only([
                'path',
                'mime_type',
                'id',
            ])
        );

        Storage::disk('public')->assertExists($file->hashName());

        $this->assertDatabaseHas('medias', [
            'path' => $file->hashName(), // because stored in root of disk
            'mime_type' => 'image/jpeg',
            'object_id' => $object->id,
        ]);
    }

    public function test_user_cannot_upload_file_greater_than_5_mb()
    {
        $user = User::factory()->create();

        $object = Obj::factory()->for($user)->create();

        Storage::fake('public');

        $file = UploadedFile::fake()->create('', 5 * 1024 + 1);

        $response = $this
            ->actingAs($user)
            ->postJson("/api/objects/{$object->id}/medias", [
                'file' => $file,
            ]);

        $response->assertInvalid(['file']);
    }

    public function test_user_cannot_upload_file_that_is_not_image_or_audio()
    {
        $user = User::factory()->create();

        $object = Obj::factory()->for($user)->create();

        Storage::fake('public');

        $file = UploadedFile::fake()->create('', 0, 'video/mp4');

        $response = $this
            ->actingAs($user)
            ->postJson("/api/objects/{$object->id}/medias", [
                'file' => $file,
            ]);

        $response->assertInvalid(['file']);
    }

    public function test_a_file_must_be_selected()
    {
        $user = User::factory()->create();

        $object = Obj::factory()->for($user)->create();

        Storage::fake('public');

        $response = $this
            ->actingAs($user)
            ->postJson("/api/objects/{$object->id}/medias");

        $response->assertInvalid(['file']);
    }

    public function test_guest_cannot_upload_files()
    {
        $object = Obj::factory()->for(User::factory())->create();

        Storage::fake('public');

        $file = UploadedFile::fake()->create('foo.m4a', 0, 'audio/x-m4a');

        $response = $this->postJson("/api/objects/{$object->id}/medias", [
            'file' => $file,
        ]);

        $response->assertUnauthorized();
    }

    public function test_user_cannot_upload_files_for_object_not_belongs_to_them()
    {
        $object = Obj::factory()
            ->for(User::factory())
            ->create();

        Storage::fake('public');

        $file = UploadedFile::fake()->create('foo.m4a', 0, 'audio/x-m4a');

        $response = $this
            ->actingAs(User::factory()->create())
            ->postJson("/api/objects/{$object->id}/medias", [
                'file' => $file,
            ]);

        $response->assertForbidden();
    }

}

<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Media;
use App\Models\Obj;
use App\Models\User;
use Carbon\Carbon;
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

        $caption = fake()->paragraph();

        $response = $this
            ->actingAs($user)
            ->postJson("/api/objects/{$object->id}/medias", [
                'file' => $file,
                'caption' => $caption,
            ]);

        $response->assertStatus(201);

        $response->assertJson(
            Media::first()->only([
                'path',
                'mime_type',
                'id',
                'caption',
            ])
        );

        Storage::disk('public')->assertExists($file->hashName());

        $this->assertDatabaseHas('medias', [
            'path' => $file->hashName(), // because stored in root of disk
            'mime_type' => 'audio/x-m4a',
            'object_id' => $object->id,
            'caption' => $caption,
        ]);
    }

    public function test_user_can_upload_jpg_to_location()
    {
        $user = User::factory()->create();

        $object = Obj::factory()->for($user)->create();

        Storage::fake('public');

        $file = UploadedFile::fake()->image('foo.jpg');

        $caption = fake()->paragraph();

        $response = $this
            ->actingAs($user)
            ->postJson("/api/objects/{$object->id}/medias", [
                'file' => $file,
                'caption' => $caption,
            ]);

        $response->assertStatus(201);

        $response->assertJson(
            Media::first()->only([
                'path',
                'mime_type',
                'id',
                'caption',
            ])
        );

        Storage::disk('public')->assertExists($file->hashName());

        $this->assertDatabaseHas('medias', [
            'path' => $file->hashName(), // because stored in root of disk
            'mime_type' => 'image/jpeg',
            'object_id' => $object->id,
            'caption' => $caption,
        ]);
    }

    public function test_user_can_upload_mp3_to_location()
    {
        $user = User::factory()->create();

        $object = Obj::factory()->for($user)->create();

        Storage::fake('public');

        $file = UploadedFile::fake()->create('foo.mp3', 0, 'audio/mpeg');

        $caption = fake()->paragraph();

        $response = $this
            ->actingAs($user)
            ->postJson("/api/objects/{$object->id}/medias", [
                'file' => $file,
                'caption' => $caption,
            ]);

        $response->assertStatus(201);

        $response->assertJson(
            Media::first()->only([
                'path',
                'mime_type',
                'id',
                'caption',
            ])
        );

        Storage::disk('public')->assertExists($file->hashName());

        $this->assertDatabaseHas('medias', [
            'path' => $file->hashName(), // because stored in root of disk
            'mime_type' => 'audio/mpeg',
            'object_id' => $object->id,
            'caption' => $caption,
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
                'caption' => fake()->paragraph(),
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
                'caption' => fake()->paragraph(),
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
            ->postJson("/api/objects/{$object->id}/medias", ['caption' => fake()->paragraph()]);

        $response->assertInvalid(['file']);
    }

    public function test_guest_cannot_upload_files()
    {
        $object = Obj::factory()->for(User::factory())->create();

        Storage::fake('public');

        $file = UploadedFile::fake()->create('foo.m4a', 0, 'audio/x-m4a');
        $caption = fake()->paragraph();

        $response = $this->postJson("/api/objects/{$object->id}/medias", [
            'file' => $file,
            'caption' => $caption,

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
        $caption = fake()->paragraph();

        $response = $this
            ->actingAs(User::factory()->create())
            ->postJson("/api/objects/{$object->id}/medias", [
                'file' => $file,
                'caption' => $caption,
            ]);

        $response->assertForbidden();
    }

    public function test_admin_can_upload_media_to_any_object()
    {
        $object = Obj::factory()
            ->for(User::factory())
            ->create();

        Storage::fake('public');

        $file = UploadedFile::fake()->create('foo.m4a', 0, 'audio/x-m4a');
        $caption = fake()->paragraph();

        $response = $this
            ->actingAs(Admin::factory()->create(), 'admin')
            ->postJson("/api/objects/{$object->id}/medias", [
                'file' => $file,
                'caption' => $caption,
            ]);

        $response->assertStatus(201);

        $response->assertJson(
            Media::first()->only([
                'path',
                'mime_type',
                'id',
                'caption',
            ])
        );

        Storage::disk('public')->assertExists($file->hashName());

        $this->assertDatabaseHas('medias', [
            'path' => $file->hashName(), // because stored in root of disk
            'mime_type' => 'audio/x-m4a',
            'object_id' => $object->id,
            'caption' => $caption,
        ]);
    }

    public function test_guest_can_index_medias_for_object()
    {
        $object = Obj::factory()
            ->for(User::factory())
            ->create();

        Storage::fake('public');

        $media = Media::factory()
            ->for($object, 'object')
            ->create();

        $response = $this->get("/api/objects/{$object->id}/medias");

        $response->assertJson(
            ['data' => [$media->only([
                'id',
                'path',
                'mime_type',
            ])]]
        );
    }

    public function test_medias_for_other_object_are_not_indexed()
    {
        // same logic as test_guests_other_locations_are_not_returned_for_an_object
        $objs = [];

        for ($i = 0; $i < 2; $i++) {
            $objs[] = Obj::factory()->for(User::factory())->create();
        }

        Media::factory()
            ->for($objs[0], 'object')
            ->create();

        $response = $this->getJson("/api/objects/{$objs[1]->id}/medias");
        $this->assertEmpty($response['data']);
    }

    public function test_media_path_actually_works()
    {
        $object = Obj::factory()
            ->for(User::factory())
            ->create();

        Storage::fake('public', [
            'url' => env('APP_URL') . '/storage',
        ]);

        $file = UploadedFile::fake()->create('foo.m4a', 0, 'audio/x-m4a');

        $response = $this
            ->actingAs(Admin::factory()->create(), 'admin')
            ->postJson("/api/objects/{$object->id}/medias", [
                'file' => $file,
                'caption' => fake()->paragraph(),
            ]);

        $this->assertEquals(
            $response['path'],
            env('APP_URL') . "/storage/{$file->hashName()}" // no real way to test if the file can be retrieved, but this is the url I think could work after studying how the disk works.
        );
    }

    public function test_admin_can_edit_media_caption_and_created_at()
    {
        Storage::fake('public', [
            'url' => env('APP_URL') . '/storage',
        ]);

        $object = Obj::factory()
            ->for(User::factory())
            ->create();

        $mediaId = Media::factory()
            ->for($object, 'object')
            ->create()
            ->id;

        $data = [
            'caption' => fake()->paragraph(),
            'created_at' => fake()->iso8601()
        ];

        $response = $this
            ->actingAs(Admin::factory()->create())
            ->patchJson("/api/medias/$mediaId", $data);

        $response->assertJson(['caption' => $data['caption']]);

        $this->assertTrue((new Carbon($data['created_at']))->equalTo($response['created_at']));

        $this->assertDatabaseHas(
            'medias',
            [
                'id' => $mediaId,
                'caption' => $data['caption'],
                'created_at' => (new Carbon($data['created_at']))
            ]
        );
    }

    public function test_admin_can_delete_medias()
    {
        Storage::fake('public', [
            'url' => env('APP_URL') . '/storage',
        ]);

        $object = Obj::factory()
            ->for(User::factory())
            ->create();

        $media = Media::factory()
            ->for($object, 'object')
            ->create();

        $response = $this
            ->actingAs(Admin::factory()->create())
            ->deleteJson("/api/medias/{$media->id}");

        $response->assertStatus(204);

        $this->assertModelMissing($media);
    }
}

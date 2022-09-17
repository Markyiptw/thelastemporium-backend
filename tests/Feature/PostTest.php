<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_index_posts()
    {
        $post = Post::factory()->create();

        $response = $this
            ->actingAs(User::factory()->create())
            ->getJson('/api/posts');

        $response->assertJson([
            'data' => [
                $post->toArray(),
            ],
        ]);
        $response->assertStatus(200);
    }

    public function test_guests_cannot_index_posts()
    {
        $response = $this
            ->getJson('/api/posts');

        $response->assertUnauthorized();
    }

    public function test_admin_can_index_posts()
    {
        $post = Post::factory()->create();

        $response = $this
            ->actingAs(Admin::factory()->create())
            ->getJson('/api/posts');

        $response->assertJson([
            'data' => [
                $post->toArray(),
            ],
        ]);
        $response->assertStatus(200);
    }

    public function test_posts_are_ordered_by_decending_created_at()
    {
        $posts = Post::factory()->count(2)->create(); // two post will has the same created at

        $posts[1]->created_at = $posts[1]->created_at->clone()->addSecond();

        $posts[1]->save(); // now the second post is the one having a greater created_at

        $posts = $posts->fresh();

        $response = $this
            ->actingAs(User::factory()->create())
            ->getJson('/api/posts');

        $response->assertJson([
            'data' => [
                $posts[1]->toArray(),
                $posts[0]->toArray(),
            ],
        ]);

        $response->assertStatus(200);
    }

    public function test_users_can_show_a_post()
    {
        $post = Post::factory()->create();

        $response = $this
            ->actingAs(User::factory()->create())
            ->getJson("/api/posts/{$post->id}");

        $response->assertJson($post->toArray());

        $response->assertStatus(200);
    }

    public function test_admin_can_show_a_post()
    {
        $post = Post::factory()->create();

        $response = $this
            ->actingAs(Admin::factory()->create())
            ->getJson("/api/posts/{$post->id}");

        $response->assertJson($post->toArray());

        $response->assertStatus(200);
    }

    public function test_guests_cannot_show_a_post()
    {
        $post = Post::factory()->create();

        $response = $this
            ->getJson("/api/posts/{$post->id}");

        $response->assertUnauthorized();
    }

    public function test_admin_can_create_post_without_created_at()
    {
        $data = Post::factory()->make()->toArray();

        $response = $this
            ->actingAs(Admin::factory()->create())
            ->postJson("/api/posts", $data);

        $response
            ->assertStatus(201)
            ->assertJson($data);

        $this->assertDatabaseHas('posts', $data);
    }

    public function test_admin_can_create_post_with_null_created_at()
    {
        $data = Post::factory()->make(['created_at' => null])->toArray();

        $dataWithoutCreatedAt = collect($data)->except('created_at')->all();

        $response = $this
            ->actingAs(Admin::factory()->create())
            ->postJson("/api/posts", $data);

        $response
            ->assertStatus(201)
            ->assertJson($dataWithoutCreatedAt);

        $this->assertTrue(
            Post::where($dataWithoutCreatedAt)
                ->whereNotNull('created_at')
                ->exists()
        );
    }

    public function test_guest_cannot_create_post()
    {
        $data = Post::factory()->make(['created_at' => null])->toArray();

        $response = $this
            ->postJson("/api/posts", $data);

        $response->assertUnauthorized();
    }

    public function test_user_cannot_create_post()
    {
        $data = Post::factory()->make(['created_at' => null])->toArray();

        $response = $this
            ->actingAs(User::factory()->create())
            ->postJson("/api/posts", $data);

        $response->assertForbidden();
    }
}

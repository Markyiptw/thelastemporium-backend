<?php

namespace Tests\Feature;

use App\Models\Draft;
use App\Models\Obj;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DraftTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_store_drafts_for_object()
    {
        $user = User::factory()->create();

        $obj = Obj::factory()->for($user)->create();

        $data = Draft::factory()->make()->toArray();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->post("/api/objects/{$obj->id}/drafts", $data);

        $response->assertStatus(201);

        $response->assertJson($data);

        $this->assertTrue(
            DB::table('drafts')
                ->whereJsonContains('to', $data['to'])
                ->whereJsonContains('cc', $data['cc'])
                ->where('message', $data['message'])
                ->exists()
        );
    }
}

<?php

namespace Tests\Feature;

use App\Models\Word;
use App\Models\User;
use App\Models\History;
use App\Models\Favorite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_user_profile()
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email
                ]
            ]);
    }

    public function test_can_get_user_history()
    {
        History::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->getJson('/api/user/history');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                    'per_page',
                    'to',
                    'total'
                ]
            ]);
    }

    public function test_can_get_user_favorites()
    {
        $word = Word::factory()->create();
        $this->user->favorites()->attach($word->id);

        $response = $this->getJson('/api/user/favorites');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                    'per_page',
                    'to',
                    'total'
                ]
            ]);
    }
}

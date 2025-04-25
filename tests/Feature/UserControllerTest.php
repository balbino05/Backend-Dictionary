<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\History;
use App\Models\Favorite;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_get_user_profile()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/user/me');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'name',
                'email',
                'created_at',
                'updated_at'
            ]);
    }

    public function test_can_get_user_history()
    {
        History::factory()->create([
            'user_id' => $this->user->id,
            'word' => 'test',
            'searched_at' => now()
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/user/me/history');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'results',
                'totalDocs',
                'previous',
                'next',
                'hasNext',
                'hasPrev'
            ]);
    }

    public function test_can_get_user_favorites()
    {
        Favorite::factory()->create([
            'user_id' => $this->user->id,
            'word' => 'test'
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/user/me/favorites');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'results',
                'totalDocs',
                'previous',
                'next',
                'hasNext',
                'hasPrev'
            ]);
    }
}

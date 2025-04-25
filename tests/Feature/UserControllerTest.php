<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\History;
use App\Models\Favorite;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserControllerTest extends TestCase
{
    public function test_can_get_user_profile()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $response = $this->getJson('/api/user/me');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'name',
                'email'
            ]);
    }

    public function test_can_get_user_history()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        History::create([
            'user_id' => $user->id,
            'word' => 'test',
            'searched_at' => now()
        ]);

        $response = $this->getJson('/api/user/me/history');

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
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        Favorite::create([
            'user_id' => $user->id,
            'word' => 'test'
        ]);

        $response = $this->getJson('/api/user/me/favorites');

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

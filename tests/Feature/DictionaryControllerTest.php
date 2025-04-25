<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Word;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DictionaryControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_search_words()
    {
        Word::factory()->create(['word' => 'test']);
        Word::factory()->create(['word' => 'testing']);
        Word::factory()->create(['word' => 'other']);

        $response = $this->actingAs($this->user)
            ->getJson('/api/entries/en?search=test');

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

    public function test_can_get_word_definition()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/entries/en/test');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'meta' => [
                    'cache',
                    'responseTime'
                ]
            ]);
    }

    public function test_can_favorite_word()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/entries/en/test/favorite');

        $response->assertStatus(204);
        $this->assertDatabaseHas('favorites', [
            'user_id' => $this->user->id,
            'word' => 'test'
        ]);
    }

    public function test_can_unfavorite_word()
    {
        $this->actingAs($this->user)
            ->postJson('/api/entries/en/test/favorite');

        $response = $this->actingAs($this->user)
            ->deleteJson('/api/entries/en/test/unfavorite');

        $response->assertStatus(204);
        $this->assertDatabaseMissing('favorites', [
            'user_id' => $this->user->id,
            'word' => 'test'
        ]);
    }
}

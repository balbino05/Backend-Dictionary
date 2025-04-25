<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Word;
use App\Models\Favorite;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DictionaryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_words()
    {
        Word::factory()->count(5)->create();

        $response = $this->getJson('/api/words');

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

    public function test_can_search_words()
    {
        Word::factory()->create(['word' => 'test']);
        Word::factory()->create(['word' => 'testing']);
        Word::factory()->create(['word' => 'other']);

        $response = $this->getJson('/api/words?search=test');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
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

    public function test_can_get_word_definition()
    {
        $word = Word::factory()->create();

        $response = $this->getJson("/api/words/{$word->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $word->id,
                    'word' => $word->word,
                    'definition' => $word->definition
                ]
            ]);
    }

    public function test_can_favorite_word()
    {
        $word = Word::factory()->create();

        $response = $this->postJson("/api/words/{$word->id}/favorite");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Word favorited successfully'
            ]);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $this->user->id,
            'word_id' => $word->id
        ]);
    }

    public function test_can_unfavorite_word()
    {
        $word = Word::factory()->create();
        $this->user->favorites()->attach($word->id);

        $response = $this->deleteJson("/api/words/{$word->id}/favorite");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Word unfavorited successfully'
            ]);

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $this->user->id,
            'word_id' => $word->id
        ]);
    }
}

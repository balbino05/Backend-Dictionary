<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Word;
use App\Models\Favorite;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class DictionaryControllerTest extends TestCase
{
    public function test_can_list_words()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        Word::create(['word' => 'test', 'language' => 'en']);
        Word::create(['word' => 'example', 'language' => 'en']);

        $response = $this->getJson('/api/entries/en');

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

    public function test_can_search_words()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        Word::create(['word' => 'test', 'language' => 'en']);
        Word::create(['word' => 'testing', 'language' => 'en']);
        Word::create(['word' => 'example', 'language' => 'en']);

        $response = $this->getJson('/api/entries/en?search=test');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'results',
                'totalDocs',
                'previous',
                'next',
                'hasNext',
                'hasPrev'
            ])
            ->assertJsonCount(2, 'results');
    }

    public function test_can_get_word_definition()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        Word::create(['word' => 'test', 'language' => 'en']);

        Http::fake([
            'https://api.dictionaryapi.dev/api/v2/entries/en/test' => Http::response([
                [
                    'word' => 'test',
                    'meanings' => [
                        [
                            'partOfSpeech' => 'noun',
                            'definitions' => [
                                ['definition' => 'A procedure for testing something.']
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $response = $this->getJson('/api/entries/en/test');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    [
                        'word',
                        'meanings' => [
                            [
                                'partOfSpeech',
                                'definitions' => [
                                    ['definition']
                                ]
                            ]
                        ]
                    ]
                ],
                'meta' => [
                    'cache',
                    'responseTime'
                ]
            ]);

        $this->assertDatabaseHas('histories', [
            'user_id' => $user->id,
            'word' => 'test'
        ]);
    }

    public function test_can_favorite_word()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $response = $this->postJson('/api/entries/en/test/favorite');

        $response->assertStatus(204);
        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'word' => 'test'
        ]);
    }

    public function test_can_unfavorite_word()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        Favorite::create([
            'user_id' => $user->id,
            'word' => 'test'
        ]);

        $response = $this->deleteJson('/api/entries/en/test/unfavorite');

        $response->assertStatus(204);
        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'word' => 'test'
        ]);
    }
}

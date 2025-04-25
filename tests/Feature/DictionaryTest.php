<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Word;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DictionaryTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        // Create a user
        $this->user = User::factory()->create([
            'email' => 'test'.uniqid().'@example.com',
            'password' => Hash::make('password')
        ]);

        // Create token using Passport
        Passport::actingAs($this->user);
        $token = $this->user->createToken('TestToken')->accessToken;
        $this->token = 'Bearer ' . $token;
    }

    public function test_welcome_endpoint()
    {
        $response = $this->get('/');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Fullstack Challenge 🏅 - Dictionary'
            ]);
    }

    public function test_search_words()
    {
        $auth = $this->createUserWithToken();

        $response = $this->withHeaders([
            'Authorization' => $auth['token']
        ])->get('/api/dictionary/search/fire?limit=4');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'results',
                'totalDocs',
                'page',
                'totalPages',
                'hasNext',
                'hasPrev'
            ]);
    }

    public function test_show_word_details()
    {
        $auth = $this->createUserWithToken();

        $response = $this->withHeaders([
            'Authorization' => $auth['token']
        ])->get('/api/dictionary/fire');

        $response->assertStatus(200);
    }

    public function test_favorite_word()
    {
        $auth = $this->createUserWithToken();

        $response = $this->withHeaders([
            'Authorization' => $auth['token']
        ])->post('/api/dictionary/fire/favorite');

        $response->assertStatus(200);
    }

    public function test_unfavorite_word()
    {
        $auth = $this->createUserWithToken();

        // First favorite the word
        $this->withHeaders([
            'Authorization' => $auth['token']
        ])->post('/api/dictionary/fire/favorite');

        // Then unfavorite it
        $response = $this->withHeaders([
            'Authorization' => $auth['token']
        ])->delete('/api/dictionary/fire/unfavorite');

        $response->assertStatus(200);
    }

    public function test_word_search()
    {
        Http::fake([
            'api.dictionaryapi.dev/api/v2/entries/en/*' => Http::response([
                [
                    'word' => 'hello',
                    'meanings' => [
                        [
                            'partOfSpeech' => 'exclamation',
                            'definitions' => [
                                ['definition' => 'Used as a greeting']
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $response = $this->withHeaders([
            'Authorization' => $this->token
        ])->getJson('/api/dictionary/search/hello');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'word',
                        'meanings' => [
                            '*' => [
                                'partOfSpeech',
                                'definitions' => [
                                    '*' => ['definition']
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
    }

    public function test_word_definition()
    {
        Http::fake([
            'api.dictionaryapi.dev/api/v2/entries/en/*' => Http::response([
                [
                    'word' => 'hello',
                    'meanings' => [
                        [
                            'partOfSpeech' => 'exclamation',
                            'definitions' => [
                                ['definition' => 'Used as a greeting']
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $response = $this->withHeaders([
            'Authorization' => $this->token
        ])->getJson('/api/dictionary/hello');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'word',
                        'meanings' => [
                            '*' => [
                                'partOfSpeech',
                                'definitions' => [
                                    '*' => ['definition']
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
    }

    public function test_word_definition_not_found()
    {
        Http::fake([
            'api.dictionaryapi.dev/api/v2/entries/en/*' => Http::response([
                'title' => 'No Definitions Found',
                'message' => 'Sorry pal, we couldn\'t find definitions for the word you were looking for.',
                'resolution' => 'You can try the search again at later time or head to the web instead.'
            ], 404)
        ]);

        $response = $this->withHeaders([
            'Authorization' => $this->token
        ])->getJson('/api/dictionary/nonexistentword');

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Word not found'
            ]);
    }
}

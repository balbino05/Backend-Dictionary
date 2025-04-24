<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Word;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DictionaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_word_search()
    {
        Word::factory()->create(['word' => 'test'.uniqid()]);

        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->accessToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token
        ])->getJson('/api/entries/en?search=test&limit=3');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'results', 'totalDocs', 'page', 'totalPages', 'hasNext', 'hasPrev'
            ]);
    }

    public function test_word_definition()
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->accessToken;

        // Mock da API externa
        Http::fake([
            'api.dictionaryapi.dev/*' => Http::response(['word' => 'test'], 200)
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token
        ])->getJson('/api/entries/en/test');

        $response->assertStatus(200)
            ->assertJson(['word' => 'test']);
    }
}

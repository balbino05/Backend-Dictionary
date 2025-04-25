<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    public function test_get_user_profile()
    {
        $auth = $this->createUserWithToken();

        $response = $this->withHeaders([
            'Authorization' => $auth['token']
        ])->get('/api/user/me');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'name',
                'email'
            ]);
    }

    public function test_get_user_history()
    {
        $auth = $this->createUserWithToken();

        // First search for a word to create history
        $this->withHeaders([
            'Authorization' => $auth['token']
        ])->get('/api/dictionary/fire');

        $response = $this->withHeaders([
            'Authorization' => $auth['token']
        ])->get('/api/user/me/history');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'results' => [
                    '*' => [
                        'word',
                        'searched_at'
                    ]
                ],
                'totalDocs',
                'page',
                'totalPages',
                'hasNext',
                'hasPrev'
            ]);
    }

    public function test_get_user_favorites()
    {
        $auth = $this->createUserWithToken();

        // First favorite a word
        $this->withHeaders([
            'Authorization' => $auth['token']
        ])->post('/api/dictionary/fire/favorite');

        $response = $this->withHeaders([
            'Authorization' => $auth['token']
        ])->get('/api/user/me/favorites');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'results' => [
                    '*' => [
                        'word',
                        'added'
                    ]
                ],
                'totalDocs',
                'page',
                'totalPages',
                'hasNext',
                'hasPrev'
            ]);
    }

    public function test_unauthorized_access()
    {
        $response = $this->get('/api/user/me');
        $response->assertStatus(401);

        $response = $this->get('/api/user/me/history');
        $response->assertStatus(401);

        $response = $this->get('/api/user/me/favorites');
        $response->assertStatus(401);
    }
}

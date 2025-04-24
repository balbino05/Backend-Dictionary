<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    public function test_user_registration()
    {
        $response = $this->postJson('/auth/signup', [
            'name' => 'Test User',
            'email' => 'test'.rand(1,1000).'@example.com', // Email único
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['id', 'name', 'token']);
    }

    public function test_user_login()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password')
        ]);

        $response = $this->postJson('/auth/signin', [
            'email' => 'test@example.com',
            'password' => 'password'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id', 'name', 'token'
            ]);
    }
}

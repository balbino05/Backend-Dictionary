<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_register_user()
    {
        $response = $this->postJson('/api/auth/signup', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password'
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email'
                    ],
                    'token'
                ],
                'message'
            ]);
    }

    public function test_cannot_register_with_existing_email()
    {
        User::factory()->create(['email' => 'test@example.com']);

        $response = $this->postJson('/api/auth/signup', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password'
        ]);

        $response->assertStatus(422);
    }

    public function test_can_login_user()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password')
        ]);

        $response = $this->postJson('/api/auth/signin', [
            'email' => 'test@example.com',
            'password' => 'password'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email'
                    ],
                    'token'
                ],
                'message'
            ]);
    }

    public function test_cannot_login_with_invalid_credentials()
    {
        $response = $this->postJson('/api/auth/signin', [
            'email' => 'test@example.com',
            'password' => 'wrong-password'
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Invalid credentials',
                'status' => 401
            ]);
    }
}

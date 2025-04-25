<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Laravel\Passport\Client;

class AuthTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    public function test_user_registration()
    {
        $email = 'test'.rand(1,1000).'@example.com';
        $response = $this->postJson('/api/auth/signup', [
            'name' => 'Test User',
            'email' => $email,
            'password' => 'password',
            'password_confirmation' => 'password'
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

        $this->assertDatabaseHas('users', [
            'email' => $email,
            'name' => 'Test User'
        ]);
    }

    public function test_user_registration_with_invalid_data()
    {
        $response = $this->postJson('/api/auth/signup', [
            'name' => '',
            'email' => 'invalid-email',
            'password' => 'short',
            'password_confirmation' => 'different'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_user_registration_with_duplicate_email()
    {
        $email = 'duplicate@example.com';
        User::factory()->create(['email' => $email]);

        $response = $this->postJson('/api/auth/signup', [
            'name' => 'Test User',
            'email' => $email,
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_user_login()
    {
        $email = 'test'.uniqid().'@example.com';
        $password = 'password';

        $user = User::factory()->create([
            'email' => $email,
            'password' => Hash::make($password)
        ]);

        $response = $this->postJson('/api/auth/signin', [
            'email' => $email,
            'password' => $password
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email'
                    ],
                    'token'
                ]
            ])
            ->assertJson([
                'message' => 'User logged in successfully'
            ]);
    }

    public function test_user_login_with_invalid_credentials()
    {
        $response = $this->postJson('/api/auth/signin', [
            'email' => 'invalid@example.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Invalid credentials',
                'status' => 401
            ]);
    }

    public function test_user_login_with_invalid_data()
    {
        $response = $this->postJson('/api/auth/signin', [
            'email' => 'invalid-email',
            'password' => ''
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }
}

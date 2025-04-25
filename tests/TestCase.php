<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Passport\Passport;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\PassportSetup;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase, PassportSetup;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        // Run Passport migrations
        Artisan::call('migrate', ['--database' => 'mysql', '--path' => 'vendor/laravel/passport/database/migrations']);

        // Setup Passport clients
        $this->setUpPassport();
    }

    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        // Configure authentication
        config([
            'auth.guards.api.driver' => 'passport',
            'auth.providers.users.model' => User::class,
            'auth.defaults.guard' => 'api',
        ]);

        return $app;
    }

    protected function createUserWithToken()
    {
        try {
            $user = User::factory()->create();
            Log::info('User created with ID: ' . $user->id);

            Passport::actingAs($user);
            $token = $user->createToken('TestToken')->accessToken;
            Log::info('Token created successfully');

            return [
                'user' => $user,
                'token' => 'Bearer ' . $token
            ];
        } catch (\Exception $e) {
            Log::error('Error creating user token: ' . $e->getMessage());
            throw $e;
        }
    }
}

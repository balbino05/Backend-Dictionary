<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Passport\Passport;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Traits\PassportSetup;
use Mockery;

abstract class TestCase extends BaseTestCase
{
    use DatabaseTransactions, PassportSetup;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        // Mock the console output for passport:install
        $mock = Mockery::mock('Illuminate\Console\OutputStyle');
        $mock->shouldReceive('askQuestion')->andReturn(true);
        $this->app->instance('Illuminate\Console\OutputStyle', $mock);

        // Run migrations if needed
        if (!$this->checkIfTablesExist()) {
            Artisan::call('migrate:fresh', ['--database' => 'mysql']);
            Artisan::call('passport:install', ['--force' => true]);
        }

        // Create a test user
        $this->user = User::factory()->create();

        // Authenticate the user
        Passport::actingAs($this->user);
    }

    protected function checkIfTablesExist()
    {
        $tables = [
            'users',
            'words',
            'favorites',
            'histories',
            'oauth_auth_codes',
            'oauth_access_tokens',
            'oauth_refresh_tokens',
            'oauth_clients',
            'oauth_personal_access_clients'
        ];

        foreach ($tables as $table) {
            if (!DB::select("SHOW TABLES LIKE '{$table}'")) {
                return false;
            }
        }

        return true;
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

        $this->app->bind(
            \App\Services\Contracts\WordServiceInterface::class,
            \App\Services\WordService::class
        );

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

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}

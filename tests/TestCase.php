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
            // Apaga tabelas do passport se existirem
            DB::statement('DROP TABLE IF EXISTS oauth_auth_codes');
            DB::statement('DROP TABLE IF EXISTS oauth_access_tokens');
            DB::statement('DROP TABLE IF EXISTS oauth_refresh_tokens');
            DB::statement('DROP TABLE IF EXISTS oauth_clients');
            DB::statement('DROP TABLE IF EXISTS oauth_personal_access_clients');
            Artisan::call('migrate:fresh', ['--database' => 'mysql']);
            Artisan::call('passport:install', ['--force' => true]);
        } else {
            // Só instala passport se não houver clients
            $clients = DB::table('oauth_clients')->count();
            if ($clients === 0) {
                Artisan::call('passport:install', ['--force' => true]);
            }
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
        $app['config']->set([
            'auth.guards.api.driver' => 'passport',
            'auth.providers.users.model' => User::class,
            'auth.defaults.guard' => 'api',
        ]);

        $app->bind(
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

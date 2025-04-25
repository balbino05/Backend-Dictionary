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

abstract class TestCase extends BaseTestCase
{
    use DatabaseTransactions, PassportSetup;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        // Check if Passport tables exist
        $tables = ['oauth_auth_codes', 'oauth_access_tokens', 'oauth_refresh_tokens', 'oauth_clients', 'oauth_personal_access_clients'];
        $missingTables = array_filter($tables, function($table) {
            return !DB::select("SHOW TABLES LIKE '{$table}'");
        });

        if (!empty($missingTables)) {
            // Run Passport migrations only if tables don't exist
            Artisan::call('migrate', ['--database' => 'mysql', '--path' => 'vendor/laravel/passport/database/migrations']);
        }

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

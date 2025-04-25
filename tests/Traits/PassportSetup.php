<?php

namespace Tests\Traits;

use Laravel\Passport\Client;
use Illuminate\Support\Facades\Artisan;

trait PassportSetup
{
    public function setUpPassport()
    {
        // Create personal access client
        $client = Client::create([
            'name' => 'Test Personal Access Client',
            'secret' => 'secret',
            'provider' => 'users',
            'redirect' => 'http://localhost',
            'personal_access_client' => true,
            'password_client' => false,
            'revoked' => false,
        ]);

        // Create password grant client
        Client::create([
            'name' => 'Test Password Grant Client',
            'secret' => 'secret',
            'provider' => 'users',
            'redirect' => 'http://localhost',
            'personal_access_client' => false,
            'password_client' => true,
            'revoked' => false,
        ]);

        // Create personal access client record
        \DB::table('oauth_personal_access_clients')->insert([
            'client_id' => $client->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

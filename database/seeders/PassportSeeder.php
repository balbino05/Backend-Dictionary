<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Laravel\Passport\Client;
use Illuminate\Support\Facades\DB;

class PassportSeeder extends Seeder
{
    public function run(): void
    {
        // Criar o personal access client
        $personalAccessClient = Client::create([
            'name' => 'Laravel Personal Access Client',
            'secret' => 'secret',
            'provider' => 'users',
            'redirect' => 'http://localhost',
            'personal_access_client' => true,
            'password_client' => false,
            'revoked' => false,
        ]);

        DB::table('oauth_personal_access_clients')->insert([
            'client_id' => $personalAccessClient->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Criar o password grant client
        Client::create([
            'name' => 'Laravel Password Grant Client',
            'secret' => 'secret',
            'provider' => 'users',
            'redirect' => 'http://localhost',
            'personal_access_client' => false,
            'password_client' => true,
            'revoked' => false,
        ]);
    }
}

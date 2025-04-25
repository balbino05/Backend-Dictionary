<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ImportWords extends Command
{
    protected $signature = 'words:import';
    protected $description = 'Download and import words from the dictionary file';

    public function handle()
    {
        $this->info('Downloading words dictionary...');

        $url = 'https://raw.githubusercontent.com/dwyl/english-words/master/words_dictionary.json';
        $path = base_path('words_dictionary.json');

        try {
            $response = Http::get($url);

            if (!$response->successful()) {
                $this->error('Failed to download words dictionary');
                return 1;
            }

            File::put($path, $response->body());
            $this->info('Words dictionary downloaded successfully');

            $this->info('Running word seeder...');
            $this->call('db:seed', ['--class' => 'WordSeeder']);

            $this->info('Words imported successfully');
            return 0;
        } catch (\Exception $e) {
            $this->error('Error importing words: ' . $e->getMessage());
            Log::error('Error importing words: ' . $e->getMessage());
            return 1;
        }
    }
}

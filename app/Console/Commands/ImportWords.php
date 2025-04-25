<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Word;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportWords extends Command
{
    protected $signature = 'words:import {file : Path to the words dictionary file}';
    protected $description = 'Import words from the dictionary file into the database';

    public function handle()
    {
        $filePath = $this->argument('file');

        if (!File::exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        $words = json_decode(File::get($filePath), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Invalid JSON file');
            return 1;
        }

        $this->info('Starting import...');
        $bar = $this->output->createProgressBar(count($words));
        $bar->start();

        DB::beginTransaction();
        try {
            foreach ($words as $word => $value) {
                Word::firstOrCreate(['word' => $word]);
                $bar->advance();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("\nError during import: " . $e->getMessage());
            return 1;
        }

        $bar->finish();
        $this->info("\nImport completed successfully!");
        return 0;
    }
}

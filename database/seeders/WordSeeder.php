<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Word;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class WordSeeder extends Seeder
{
    public function run(): void
    {
        $jsonPath = base_path('words_dictionary.json');

        if (!File::exists($jsonPath)) {
            Log::error('Words dictionary file not found at: ' . $jsonPath);
            return;
        }

        $words = json_decode(File::get($jsonPath), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Error decoding words dictionary JSON: ' . json_last_error_msg());
            return;
        }

        $batchSize = 1000;
        $wordsToInsert = [];
        $count = 0;

        foreach ($words as $word => $value) {
            $wordsToInsert[] = [
                'word' => $word,
                'language' => 'en',
                'created_at' => now(),
                'updated_at' => now()
            ];

            if (count($wordsToInsert) >= $batchSize) {
                Word::insert($wordsToInsert);
                $count += count($wordsToInsert);
                $wordsToInsert = [];
                Log::info("Inserted {$count} words so far...");
            }
        }

        // Insert remaining words
        if (!empty($wordsToInsert)) {
            Word::insert($wordsToInsert);
            $count += count($wordsToInsert);
        }

        Log::info("Total words inserted: {$count}");
    }
}

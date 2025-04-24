<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WordsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $words = ['apple', 'banana', 'computer', 'data', 'example', 'function'];

        foreach ($words as $word) {
            \App\Models\Word::create([
                'word' => $word,
                'language' => 'en'
            ]);
        }
    }
}

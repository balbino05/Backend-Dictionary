<?php

namespace App\Services;

use App\Repositories\Contracts\WordRepositoryInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\History;

class WordService implements WordServiceInterface
{
    protected $wordRepository;

    public function __construct(WordRepositoryInterface $wordRepository)
    {
        $this->wordRepository = $wordRepository;
    }

    public function searchWords($search, $limit = 10, $page = 1)
    {
        return $this->wordRepository->search($search, $limit, $page);
    }

    public function getWordDefinition($word)
    {
        // Register in history
        History::create([
            'user_id' => auth()->id(),
            'word' => $word,
            'searched_at' => now()
        ]);

        $cacheKey = 'word_definition_'.$word;
        $duration = now()->addHours(24); // Cache for 24 hours

        return Cache::remember($cacheKey, $duration, function () use ($word) {
            $apiResponse = Http::timeout(3)
                ->retry(3, 100)
                ->get("https://api.dictionaryapi.dev/api/v2/entries/en/{$word}");

            return $apiResponse->successful() ? $apiResponse->json() : null;
        });
    }

    public function importWords()
    {
        $words = json_decode(file_get_contents('https://raw.githubusercontent.com/dwyl/english-words/master/words_dictionary.json'), true);

        $batchSize = 1000;
        $wordsToInsert = [];

        foreach ($words as $word => $value) {
            $wordsToInsert[] = [
                'word' => $word,
                'language' => 'en',
                'created_at' => now(),
                'updated_at' => now()
            ];

            if (count($wordsToInsert) >= $batchSize) {
                $this->wordRepository->create($wordsToInsert);
                $wordsToInsert = [];
            }
        }

        if (!empty($wordsToInsert)) {
            $this->wordRepository->create($wordsToInsert);
        }
    }
}

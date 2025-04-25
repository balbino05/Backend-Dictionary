<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\History;
use App\Models\Favorite;

class DictionaryController extends Controller
{
   public function index(Request $request, $word)
   {
       $response = Cache::remember('word_search_'.$word, now()->addHours(24), function () use ($word) {
           $apiResponse = Http::timeout(3)
               ->retry(3, 100)
               ->get("https://api.dictionaryapi.dev/api/v2/entries/en/{$word}");

           return $apiResponse->successful() ? $apiResponse->json() : null;
       });

       if (!$response) {
           return response()->json(['message' => 'Word not found'], 404);
       }

       return response()->json([
           'data' => $response,
           'meta' => [
               'cache' => Cache::has('word_search_'.$word) ? 'HIT' : 'MISS',
               'responseTime' => round((microtime(true) - LARAVEL_START) * 1000).'ms'
           ]
       ]);
   }

    public function show(Request $request, $word)
   {
      // Registrar no histórico
      History::create([
         'user_id' => auth()->id(),
         'word' => $word,
         'searched_at' => now()
      ]);

      $cacheKey = 'word_definition_'.$word;
      $duration = now()->addHours(24); // Cache por 24 horas

      $response = Cache::remember($cacheKey, $duration, function () use ($word) {
         $apiResponse = Http::timeout(3)
               ->retry(3, 100)
               ->get("https://api.dictionaryapi.dev/api/v2/entries/en/{$word}");

         return $apiResponse->successful() ? $apiResponse->json() : null;
      });

      if (!$response) {
         return response()->json(['message' => 'Word not found'], 404);
      }

      return response()->json([
          'data' => $response,
          'meta' => [
              'cache' => Cache::has($cacheKey) ? 'HIT' : 'MISS',
              'responseTime' => round((microtime(true) - LARAVEL_START) * 1000).'ms'
          ]
      ]);
   }

    public function favorite($word)
    {
        Favorite::firstOrCreate([
            'user_id' => auth()->id(),
            'word' => $word
        ]);

        return response()->noContent();
    }

    public function unfavorite($word)
    {
        Favorite::where('user_id', auth()->id())
            ->where('word', $word)
            ->delete();

        return response()->noContent();
    }
}

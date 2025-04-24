<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\History;
use App\Models\Favorite;

class DictionaryController extends Controller
{
   public function index(Request $request)
   {
       $request->validate([
           'search' => 'sometimes|string|min:2',
           'limit' => 'sometimes|integer|min:1|max:50'
       ]);

       $search = $request->query('search', '');
       $limit = $request->query('limit', 10);

       $query = \App\Models\Word::where('language', 'en')
           ->when($search, function ($query, $search) {
               return $query->where('word', 'like', $search.'%');
           });

       $words = $query->orderBy('word')
           ->paginate($limit);

       return response()->json([
           'results' => $words->pluck('word'),
           'totalDocs' => $words->total(),
           'page' => $words->currentPage(),
           'totalPages' => $words->lastPage(),
           'hasNext' => $words->hasMorePages(),
           'hasPrev' => $words->currentPage() > 1,
           'X-Cache' => 'MISS', // Implementar cache depois
           'X-Response-Time' => round((microtime(true) - LARAVEL_START) * 1000).'ms'
       ]);
   }

    public function show(Request $request, $word)
   {
      // Registrar no histórico
      History::create([
         'user_id' => auth()->id(),
         'word' => $word
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

      return response()->json($response)
         ->header('X-Cache', Cache::has($cacheKey) ? 'HIT' : 'MISS')
         ->header('X-Response-Time', round((microtime(true) - LARAVEL_START) * 1000).'ms');
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

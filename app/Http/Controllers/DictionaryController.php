<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\History;
use App\Models\Favorite;
use App\Models\Word;
use Illuminate\Support\Facades\DB;

class DictionaryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $limit = (int) $request->query('limit', 10);
        $page = (int) $request->query('page', 1);

        $query = Word::query();

        if ($search) {
            $query->where('word', 'like', "{$search}%");
        }

        $totalDocs = $query->count();
        $totalPages = ceil($totalDocs / $limit);

        $words = $query->orderBy('word')
            ->skip(($page - 1) * $limit)
            ->take($limit)
            ->get();

        return response()->json([
            'results' => $words->pluck('word'),
            'totalDocs' => $totalDocs,
            'page' => $page,
            'totalPages' => $totalPages,
            'hasNext' => $page < $totalPages,
            'hasPrev' => $page > 1
        ]);
    }

    public function show(Request $request, $word)
    {
        $startTime = microtime(true);

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

        $responseTime = round((microtime(true) - $startTime) * 1000);

        return response()->json([
            'data' => $response,
            'meta' => [
                'cache' => Cache::has($cacheKey) ? 'HIT' : 'MISS',
                'responseTime' => $responseTime . 'ms'
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

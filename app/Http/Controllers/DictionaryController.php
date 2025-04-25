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
        $cursor = $request->query('cursor');

        $query = Word::query();

        if ($search) {
            $query->where('word', 'like', "{$search}%");
        }

        if ($cursor) {
            $cursorWord = Word::find($cursor);
            if ($cursorWord) {
                $query->where('id', '>', $cursorWord->id);
            }
        }

        $words = $query->orderBy('id')
            ->limit($limit + 1)
            ->get();

        $hasNext = $words->count() > $limit;
        $words = $words->take($limit);

        $nextCursor = $hasNext ? $words->last()->id : null;
        $previousCursor = $cursor ? Word::where('id', '<', $cursor)->orderByDesc('id')->first()?->id : null;

        return response()->json([
            'results' => $words->pluck('word'),
            'totalDocs' => Word::count(),
            'previous' => $previousCursor,
            'next' => $nextCursor,
            'hasNext' => $hasNext,
            'hasPrev' => $previousCursor !== null
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

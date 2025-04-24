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
            'search' => 'sometimes|string',
            'limit' => 'sometimes|integer|min:1|max:50'
        ]);

        // Implementar lógica de busca local
        // ...

        return response()->json([
            'results' => [],
            'totalDocs' => 0,
            'page' => 1,
            'totalPages' => 1,
            'hasNext' => false,
            'hasPrev' => false
        ]);
    }

    public function show(Request $request, $word)
    {
        // Registrar no histórico
        History::create([
            'user_id' => auth()->id(),
            'word' => $word
        ]);

        // Buscar na Free Dictionary API
        $response = Http::get("https://api.dictionaryapi.dev/api/v2/entries/en/{$word}");

        if ($response->failed()) {
            return response()->json(['message' => 'Word not found'], 404);
        }

        return $response->json();
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

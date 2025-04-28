<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\History;
use App\Models\Favorite;
use App\Models\Word;
use Illuminate\Support\Facades\DB;
use App\Services\Contracts\WordServiceInterface;

class DictionaryController extends Controller
{
    protected $wordService;

    public function __construct(WordServiceInterface $wordService)
    {
        $this->wordService = $wordService;
    }

    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $limit = (int) $request->query('limit', 10);
        $page = (int) $request->query('page', 1);

        return response()->json($this->wordService->searchWords($search, $limit, $page));
    }

    public function show(Request $request, $word)
    {
        $startTime = microtime(true);
        $response = $this->wordService->getWordDefinition($word);
        $endTime = microtime(true);

        if (!$response) {
            return response()->json([
                'message' => 'Word not found'
            ], 404);
        }

        return response()->json($response)
            ->header('x-cache', 'MISS')
            ->header('x-response-time', round(($endTime - $startTime) * 1000));
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

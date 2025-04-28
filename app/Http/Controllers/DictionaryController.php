<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Contracts\WordServiceInterface;
use App\Services\Contracts\HistoryServiceInterface;
use App\Services\Contracts\FavoriteServiceInterface;
use Illuminate\Support\Facades\Cache;

class DictionaryController extends Controller
{
    protected $wordService;
    protected $historyService;
    protected $favoriteService;

    public function __construct(
        WordServiceInterface $wordService,
        HistoryServiceInterface $historyService,
        FavoriteServiceInterface $favoriteService
    ) {
        $this->wordService = $wordService;
        $this->historyService = $historyService;
        $this->favoriteService = $favoriteService;
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
        $cacheKey = 'word_definition_'.$word;
        $isCached = Cache::has($cacheKey);

        $response = $this->wordService->getWordDefinition($word);
        $endTime = microtime(true);

        if (!$response) {
            return response()->json([
                'message' => 'Word not found'
            ], 404);
        }

        return response()->json($response)
            ->header('x-cache', $isCached ? 'HIT' : 'MISS')
            ->header('x-response-time', round(($endTime - $startTime) * 1000));
    }

    public function favorite($word)
    {
        $this->favoriteService->addFavorite($word);
        return response()->noContent();
    }

    public function unfavorite($word)
    {
        $this->favoriteService->removeFavorite($word);
        return response()->noContent();
    }
}

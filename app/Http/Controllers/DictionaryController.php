<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Contracts\WordServiceInterface;
use App\Services\Contracts\HistoryServiceInterface;
use App\Services\Contracts\FavoriteServiceInterface;
use Illuminate\Support\Facades\Cache;
use OpenApi\Annotations as OA;
use App\Http\Requests\DictionaryRequest;
use App\Services\Contracts\DictionaryServiceInterface;

/**
 * @OA\Tag(
 *     name="Dictionary",
 *     description="Dictionary operations"
 * )
 */
class DictionaryController extends Controller
{
    protected $wordService;
    protected $historyService;
    protected $favoriteService;
    protected $dictionaryService;

    public function __construct(
        WordServiceInterface $wordService,
        HistoryServiceInterface $historyService,
        FavoriteServiceInterface $favoriteService,
        DictionaryServiceInterface $dictionaryService
    ) {
        $this->wordService = $wordService;
        $this->historyService = $historyService;
        $this->favoriteService = $favoriteService;
        $this->dictionaryService = $dictionaryService;
    }

    /**
     * @OA\Get(
     *     path="/api/dictionary",
     *     summary="Search for words",
     *     tags={"Dictionary"},
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         description="Search query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of results per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Word")),
     *             @OA\Property(property="meta", ref="#/components/schemas/Pagination")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = $request->input('q');
        $limit = $request->input('limit', 10);
        $page = $request->input('page', 1);

        return $this->wordService->search($query, $limit, $page);
    }

    /**
     * @OA\Get(
     *     path="/api/dictionary/{word}",
     *     summary="Get word details",
     *     tags={"Dictionary"},
     *     @OA\Parameter(
     *         name="word",
     *         in="path",
     *         description="Word to look up",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Word")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Word not found"
     *     )
     * )
     */
    public function show($word)
    {
        $cacheKey = "word:{$word}";
        $response = $this->wordService->getWord($word);

        if (cache()->has($cacheKey)) {
            $response->header('x-cache', 'HIT');
        } else {
            $response->header('x-cache', 'MISS');
        }

        $response->header('x-response-time', microtime(true) - LARAVEL_START);

        return $response;
    }

    /**
     * @OA\Post(
     *     path="/api/dictionary/{word}/favorite",
     *     summary="Add word to favorites",
     *     tags={"Dictionary"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="word",
     *         in="path",
     *         description="Word to favorite",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Word added to favorites",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Word added to favorites")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Word not found"
     *     )
     * )
     */
    public function favorite($word)
    {
        return $this->favoriteService->addFavorite($word);
    }

    /**
     * @OA\Delete(
     *     path="/api/dictionary/{word}/favorite",
     *     summary="Remove word from favorites",
     *     tags={"Dictionary"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="word",
     *         in="path",
     *         description="Word to unfavorite",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Word removed from favorites",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Word removed from favorites")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Word not found"
     *     )
     * )
     */
    public function unfavorite($word)
    {
        return $this->favoriteService->removeFavorite($word);
    }

    /**
     * @OA\Get(
     *     path="/api/dictionary",
     *     summary="Get all dictionary entries",
     *     tags={"Dictionary"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of dictionary entries",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Dictionary")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function getAll()
    {
        return $this->dictionaryService->getAll();
    }

    /**
     * @OA\Get(
     *     path="/api/dictionary/{id}",
     *     summary="Get dictionary entry by ID",
     *     tags={"Dictionary"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dictionary entry details",
     *         @OA\JsonContent(ref="#/components/schemas/Dictionary")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Dictionary entry not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function getById($id)
    {
        return $this->dictionaryService->getById($id);
    }

    /**
     * @OA\Post(
     *     path="/api/dictionary",
     *     summary="Create new dictionary entry",
     *     tags={"Dictionary"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="word", type="string", example="example"),
     *             @OA\Property(property="definition", type="string", example="A sample definition"),
     *             @OA\Property(property="example", type="string", example="This is an example sentence.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Dictionary entry created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Dictionary")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function store(DictionaryRequest $request)
    {
        return $this->dictionaryService->create($request->validated());
    }

    /**
     * @OA\Put(
     *     path="/api/dictionary/{id}",
     *     summary="Update dictionary entry",
     *     tags={"Dictionary"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="word", type="string", example="updated example"),
     *             @OA\Property(property="definition", type="string", example="An updated definition"),
     *             @OA\Property(property="example", type="string", example="This is an updated example sentence.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dictionary entry updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Dictionary")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Dictionary entry not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function update(DictionaryRequest $request, $id)
    {
        return $this->dictionaryService->update($id, $request->validated());
    }

    /**
     * @OA\Delete(
     *     path="/api/dictionary/{id}",
     *     summary="Delete dictionary entry",
     *     tags={"Dictionary"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Dictionary entry deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Dictionary entry not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function destroy($id)
    {
        return $this->dictionaryService->delete($id);
    }
}

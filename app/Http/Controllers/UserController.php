<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Contracts\UserServiceInterface;
use App\Services\Contracts\HistoryServiceInterface;
use App\Services\Contracts\FavoriteServiceInterface;

class UserController extends Controller
{
    protected $userService;
    protected $historyService;
    protected $favoriteService;

    public function __construct(
        UserServiceInterface $userService,
        HistoryServiceInterface $historyService,
        FavoriteServiceInterface $favoriteService
    ) {
        $this->userService = $userService;
        $this->historyService = $historyService;
        $this->favoriteService = $favoriteService;
    }

    public function getProfile()
    {
        return response()->json($this->userService->getProfile());
    }

    public function getHistory(Request $request)
    {
        $limit = (int) $request->query('limit', 10);
        $page = (int) $request->query('page', 1);

        return response()->json($this->historyService->getUserHistory($limit, $page));
    }

    public function getFavorites(Request $request)
    {
        $limit = (int) $request->query('limit', 10);
        $page = (int) $request->query('page', 1);

        return response()->json($this->favoriteService->getUserFavorites($limit, $page));
    }
}

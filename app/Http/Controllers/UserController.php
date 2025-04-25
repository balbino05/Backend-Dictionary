<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\History;
use App\Models\Favorite;

class UserController extends Controller
{
    public function profile()
    {
        return auth()->user();
    }

    public function history(Request $request)
    {
        $limit = (int) $request->query('limit', 10);
        $page = (int) $request->query('page', 1);

        $query = History::where('user_id', auth()->id())
            ->orderByDesc('created_at');

        $totalDocs = $query->count();
        $totalPages = ceil($totalDocs / $limit);

        $history = $query->skip(($page - 1) * $limit)
            ->take($limit)
            ->get();

        return response()->json([
            'results' => $history->map(function ($item) {
                return [
                    'word' => $item->word,
                    'added' => $item->searched_at
                ];
            }),
            'totalDocs' => $totalDocs,
            'page' => $page,
            'totalPages' => $totalPages,
            'hasNext' => $page < $totalPages,
            'hasPrev' => $page > 1
        ]);
    }

    public function favorites(Request $request)
    {
        $limit = (int) $request->query('limit', 10);
        $page = (int) $request->query('page', 1);

        $query = Favorite::where('user_id', auth()->id())
            ->orderByDesc('created_at');

        $totalDocs = $query->count();
        $totalPages = ceil($totalDocs / $limit);

        $favorites = $query->skip(($page - 1) * $limit)
            ->take($limit)
            ->get();

        return response()->json([
            'results' => $favorites->map(function ($item) {
                return [
                    'word' => $item->word,
                    'added' => $item->created_at
                ];
            }),
            'totalDocs' => $totalDocs,
            'page' => $page,
            'totalPages' => $totalPages,
            'hasNext' => $page < $totalPages,
            'hasPrev' => $page > 1
        ]);
    }
}

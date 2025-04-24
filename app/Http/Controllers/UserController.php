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
        $perPage = $request->query('limit', 10);

        $history = History::where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return response()->json([
            'results' => $history->items(),
            'totalDocs' => $history->total(),
            'page' => $history->currentPage(),
            'totalPages' => $history->lastPage(),
            'hasNext' => $history->hasMorePages(),
            'hasPrev' => $history->currentPage() > 1
        ]);
    }

    public function favorites(Request $request)
    {
        $perPage = $request->query('limit', 10);

        $favorites = Favorite::where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return response()->json([
            'results' => $favorites->items(),
            'totalDocs' => $favorites->total(),
            'page' => $favorites->currentPage(),
            'totalPages' => $favorites->lastPage(),
            'hasNext' => $favorites->hasMorePages(),
            'hasPrev' => $favorites->currentPage() > 1
        ]);
    }
}

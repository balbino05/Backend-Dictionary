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
        $cursor = $request->query('cursor');

        $query = History::where('user_id', auth()->id())
            ->orderByDesc('created_at');

        if ($cursor) {
            $cursorHistory = History::find($cursor);
            if ($cursorHistory) {
                $query->where('id', '<', $cursorHistory->id);
            }
        }

        $history = $query->limit($limit + 1)->get();
        $hasNext = $history->count() > $limit;
        $history = $history->take($limit);

        $nextCursor = $hasNext ? $history->last()->id : null;
        $previousCursor = $cursor ? History::where('id', '>', $cursor)->orderBy('id')->first()?->id : null;

        return response()->json([
            'results' => $history->map(function ($item) {
                return [
                    'word' => $item->word,
                    'added' => $item->searched_at
                ];
            }),
            'totalDocs' => History::where('user_id', auth()->id())->count(),
            'previous' => $previousCursor,
            'next' => $nextCursor,
            'hasNext' => $hasNext,
            'hasPrev' => $previousCursor !== null
        ]);
    }

    public function favorites(Request $request)
    {
        $limit = (int) $request->query('limit', 10);
        $cursor = $request->query('cursor');

        $query = Favorite::where('user_id', auth()->id())
            ->orderByDesc('created_at');

        if ($cursor) {
            $cursorFavorite = Favorite::find($cursor);
            if ($cursorFavorite) {
                $query->where('id', '<', $cursorFavorite->id);
            }
        }

        $favorites = $query->limit($limit + 1)->get();
        $hasNext = $favorites->count() > $limit;
        $favorites = $favorites->take($limit);

        $nextCursor = $hasNext ? $favorites->last()->id : null;
        $previousCursor = $cursor ? Favorite::where('id', '>', $cursor)->orderBy('id')->first()?->id : null;

        return response()->json([
            'results' => $favorites->map(function ($item) {
                return [
                    'word' => $item->word,
                    'added' => $item->created_at
                ];
            }),
            'totalDocs' => Favorite::where('user_id', auth()->id())->count(),
            'previous' => $previousCursor,
            'next' => $nextCursor,
            'hasNext' => $hasNext,
            'hasPrev' => $previousCursor !== null
        ]);
    }
}

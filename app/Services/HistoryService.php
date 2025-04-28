<?php

namespace App\Services;

use App\Services\Contracts\HistoryServiceInterface;
use App\Models\History;
use Illuminate\Support\Facades\Auth;

class HistoryService implements HistoryServiceInterface
{
    public function getUserHistory($limit = 10, $page = 1)
    {
        $user = Auth::user();
        return History::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($limit, ['*'], 'page', $page);
    }

    public function addToHistory($word)
    {
        $user = Auth::user();
        return History::create([
            'user_id' => $user->id,
            'word' => $word,
            'searched_at' => now(),
        ]);
    }
}

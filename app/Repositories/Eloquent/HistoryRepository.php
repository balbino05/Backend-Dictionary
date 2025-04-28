<?php

namespace App\Repositories\Eloquent;

use App\Models\History;
use App\Repositories\Contracts\HistoryRepositoryInterface;

class HistoryRepository extends BaseRepository implements HistoryRepositoryInterface
{
    public function __construct(History $model)
    {
        parent::__construct($model);
    }

    public function getUserHistory($userId, $limit = 10, $page = 1)
    {
        $query = $this->model->where('user_id', $userId)
            ->orderBy('searched_at', 'desc');

        $totalDocs = $query->count();
        $totalPages = ceil($totalDocs / $limit);

        $history = $query->skip(($page - 1) * $limit)
            ->take($limit)
            ->get();

        return [
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
        ];
    }

    public function addToHistory($userId, $word)
    {
        return $this->create([
            'user_id' => $userId,
            'word' => $word,
            'searched_at' => now()
        ]);
    }
}

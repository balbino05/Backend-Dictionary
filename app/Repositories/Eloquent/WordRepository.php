<?php

namespace App\Repositories\Eloquent;

use App\Models\Word;
use App\Repositories\Contracts\WordRepositoryInterface;
use Illuminate\Support\Collection;

class WordRepository extends BaseRepository implements WordRepositoryInterface
{
    public function __construct(Word $model)
    {
        parent::__construct($model);
    }

    public function search($search, $limit = 10, $page = 1)
    {
        $query = $this->model->query();

        if ($search) {
            $query->where('word', 'like', "{$search}%");
        }

        $totalDocs = $query->count();
        $totalPages = ceil($totalDocs / $limit);

        $words = $query->orderBy('word')
            ->skip(($page - 1) * $limit)
            ->take($limit)
            ->get();

        return [
            'results' => $words->pluck('word'),
            'totalDocs' => $totalDocs,
            'page' => $page,
            'totalPages' => $totalPages,
            'hasNext' => $page < $totalPages,
            'hasPrev' => $page > 1
        ];
    }

    public function findByWord($word)
    {
        return $this->model->where('word', $word)->first();
    }

    public function getTotalCount()
    {
        return $this->model->count();
    }
}

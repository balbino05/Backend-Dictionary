<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\FavoriteRepositoryInterface;
use App\Models\Favorite;
use App\Models\Word;

class FavoriteRepository implements FavoriteRepositoryInterface
{
    public function getUserFavorites($userId, $limit = 10, $page = 1)
    {
        return Favorite::where('user_id', $userId)
            ->with('word')
            ->orderBy('created_at', 'desc')
            ->paginate($limit, ['*'], 'page', $page);
    }

    public function addFavorite($userId, $word)
    {
        $wordModel = Word::where('word', $word)->first();
        if (!$wordModel) {
            return null;
        }
        return Favorite::firstOrCreate([
            'user_id' => $userId,
            'word_id' => $wordModel->id
        ]);
    }

    public function removeFavorite($userId, $word)
    {
        $wordModel = Word::where('word', $word)->first();
        if (!$wordModel) {
            return false;
        }
        return Favorite::where('user_id', $userId)
            ->where('word_id', $wordModel->id)
            ->delete();
    }

    public function isFavorite($userId, $word)
    {
        $wordModel = Word::where('word', $word)->first();
        if (!$wordModel) {
            return false;
        }
        return Favorite::where('user_id', $userId)
            ->where('word_id', $wordModel->id)
            ->exists();
    }

    public function all()
    {
        return Favorite::all();
    }

    public function find($id)
    {
        return Favorite::find($id);
    }

    public function create(array $data)
    {
        return Favorite::create($data);
    }

    public function update($id, array $data)
    {
        $favorite = Favorite::find($id);
        if ($favorite) {
            $favorite->update($data);
        }
        return $favorite;
    }

    public function delete($id)
    {
        return Favorite::destroy($id);
    }

    public function paginate($perPage = 10, $page = 1)
    {
        return Favorite::orderBy('created_at', 'desc')->paginate($perPage, ['*'], 'page', $page);
    }
}

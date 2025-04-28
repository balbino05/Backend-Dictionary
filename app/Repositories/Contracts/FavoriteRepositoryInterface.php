<?php

namespace App\Repositories\Contracts;

interface FavoriteRepositoryInterface extends RepositoryInterface
{
    public function getUserFavorites($userId, $limit = 10, $page = 1);
    public function addFavorite($userId, $word);
    public function removeFavorite($userId, $word);
    public function isFavorite($userId, $word);
}

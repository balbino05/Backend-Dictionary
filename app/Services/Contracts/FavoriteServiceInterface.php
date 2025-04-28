<?php

namespace App\Services\Contracts;

interface FavoriteServiceInterface
{
    public function getUserFavorites($limit = 10, $page = 1);
    public function addFavorite($word);
    public function removeFavorite($word);
    public function isFavorite($word);
}

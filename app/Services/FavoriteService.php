<?php

namespace App\Services;

use App\Repositories\Contracts\FavoriteRepositoryInterface;
use App\Services\Contracts\FavoriteServiceInterface;

class FavoriteService implements FavoriteServiceInterface
{
    protected $favoriteRepository;

    public function __construct(FavoriteRepositoryInterface $favoriteRepository)
    {
        $this->favoriteRepository = $favoriteRepository;
    }

    public function getUserFavorites($limit = 10, $page = 1)
    {
        return $this->favoriteRepository->getUserFavorites(auth()->id(), $limit, $page);
    }

    public function addFavorite($word)
    {
        return $this->favoriteRepository->addFavorite(auth()->id(), $word);
    }

    public function removeFavorite($word)
    {
        return $this->favoriteRepository->removeFavorite(auth()->id(), $word);
    }

    public function isFavorite($word)
    {
        return $this->favoriteRepository->isFavorite(auth()->id(), $word);
    }
}

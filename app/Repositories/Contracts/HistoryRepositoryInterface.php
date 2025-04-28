<?php

namespace App\Repositories\Contracts;

interface HistoryRepositoryInterface extends RepositoryInterface
{
    public function getUserHistory($userId, $limit = 10, $page = 1);
    public function addToHistory($userId, $word);
}

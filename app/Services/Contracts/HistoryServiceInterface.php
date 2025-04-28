<?php

namespace App\Services\Contracts;

interface HistoryServiceInterface
{
    public function getUserHistory($limit = 10, $page = 1);
    public function addToHistory($word);
}

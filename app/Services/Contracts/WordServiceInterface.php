<?php

namespace App\Services\Contracts;

interface WordServiceInterface
{
    public function searchWords($search, $limit = 10, $page = 1);
    public function getWordDefinition($word);
    public function importWords();
}

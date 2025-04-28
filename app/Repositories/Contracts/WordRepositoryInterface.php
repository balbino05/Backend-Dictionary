<?php

namespace App\Repositories\Contracts;

interface WordRepositoryInterface extends RepositoryInterface
{
    public function search($search, $limit = 10, $page = 1);
    public function findByWord($word);
    public function getTotalCount();
}

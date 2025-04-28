<?php

namespace App\Repositories\Contracts;

interface UserRepositoryInterface extends RepositoryInterface
{
    public function findByEmail($email);
    public function createToken($user);
}

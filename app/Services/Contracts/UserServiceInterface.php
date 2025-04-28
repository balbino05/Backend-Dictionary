<?php

namespace App\Services\Contracts;

interface UserServiceInterface
{
    public function register(array $data);
    public function login(array $data);
    public function getProfile();
}

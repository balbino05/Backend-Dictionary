<?php

namespace App\Services\Contracts;

interface AuthServiceInterface
{
    public function register(array $data);
    public function login(array $credentials);
    public function logout();
    public function refresh();
    public function me();
}

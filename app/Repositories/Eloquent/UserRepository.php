<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Laravel\Passport\PersonalAccessTokenResult;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function findByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function createToken($user): PersonalAccessTokenResult
    {
        return $user->createToken('Personal Access Token');
    }
}

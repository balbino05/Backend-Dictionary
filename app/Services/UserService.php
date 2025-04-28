<?php

namespace App\Services;

use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Services\Contracts\UserServiceInterface;

class UserService implements UserServiceInterface
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        $user = $this->userRepository->create($data);
        $token = $this->userRepository->createToken($user);

        return [
            'id' => $user->id,
            'name' => $user->name,
            'token' => 'Bearer ' . $token->accessToken
        ];
    }

    public function login(array $data)
    {
        $user = $this->userRepository->findByEmail($data['email']);

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $this->userRepository->createToken($user);

        return [
            'id' => $user->id,
            'name' => $user->name,
            'token' => 'Bearer ' . $token->accessToken
        ];
    }

    public function getProfile()
    {
        return auth()->user();
    }
}

<?php

namespace App\Services;

use App\Models\User;
use App\Services\Contracts\AuthServiceInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService implements AuthServiceInterface
{
    public function register(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $token = $user->createToken('auth_token')->accessToken;

        return response()->json([
            'data' => [
                'user' => $user,
                'token' => $token
            ],
            'message' => 'User registered successfully'
        ], 201);
    }

    public function login(array $credentials)
    {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials',
                'status' => 401
            ], 401);
        }

        $token = $user->createToken('auth_token')->accessToken;

        return response()->json([
            'data' => [
                'user' => $user,
                'token' => $token
            ],
            'message' => 'Logged in successfully'
        ]);
    }

    public function logout()
    {
        Auth::user()->tokens->each(function ($token) {
            $token->delete();
        });

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    public function refresh()
    {
        $user = Auth::user();
        $user->tokens->each(function ($token) {
            $token->delete();
        });

        $token = $user->createToken('auth_token')->accessToken;

        return response()->json([
            'data' => [
                'token' => $token
            ],
            'message' => 'Token refreshed successfully'
        ]);
    }

    public function me()
    {
        return response()->json([
            'data' => Auth::user()
        ]);
    }
}

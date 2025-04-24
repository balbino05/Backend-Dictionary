<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function signup(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $token = $user->createToken('auth_token')->accessToken;

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'token' => 'Bearer ' . $token
        ], 201);
    }

    public function signin(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (!auth()->attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = auth()->user();
        $token = $user->createToken('auth_token')->accessToken;

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'token' => 'Bearer ' . $token
        ]);
    }
}

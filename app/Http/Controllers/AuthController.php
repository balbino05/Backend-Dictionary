<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Contracts\UserServiceInterface;

class AuthController extends Controller
{
    protected $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        return response()->json($this->userService->register($request->all()));
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        return response()->json($this->userService->login($request->all()));
    }
}

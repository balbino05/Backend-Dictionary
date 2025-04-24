<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DictionaryController;
use App\Http\Controllers\UserController;

// Rota de boas-vindas
Route::get('/', function () {
    return response()->json(['message' => 'Fullstack Challenge 🏅 - Dictionary']);
});

// Rotas de autenticação
Route::post('/auth/signup', [AuthController::class, 'signup']);
Route::post('/auth/signin', [AuthController::class, 'signin']);

// Rotas protegidas
Route::middleware('auth:api')->group(function () {
    // Rotas do dicionário
    Route::get('/entries/en', [DictionaryController::class, 'index']);
    Route::get('/entries/en/{word}', [DictionaryController::class, 'show']);
    Route::post('/entries/en/{word}/favorite', [DictionaryController::class, 'favorite']);
    Route::delete('/entries/en/{word}/unfavorite', [DictionaryController::class, 'unfavorite']);

    // Rotas do usuário
    Route::get('/user/me', [UserController::class, 'profile']);
    Route::get('/user/me/history', [UserController::class, 'history']);
    Route::get('/user/me/favorites', [UserController::class, 'favorites']);
});

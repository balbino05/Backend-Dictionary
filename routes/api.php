<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DictionaryController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return response()->json(['message' => 'Fullstack Challenge 🏅 - Dictionary']);
});

Route::prefix('auth')->group(function () {
    Route::post('/signup', [AuthController::class, 'signup']);
    Route::post('/signin', [AuthController::class, 'signin']);
});

Route::middleware('auth:api')->group(function () {
    Route::prefix('entries/en')->group(function () {
        Route::get('/', [DictionaryController::class, 'index']);
        Route::get('/{word}', [DictionaryController::class, 'show']);
        Route::post('/{word}/favorite', [DictionaryController::class, 'favorite']);
        Route::delete('/{word}/unfavorite', [DictionaryController::class, 'unfavorite']);
    });

    Route::prefix('user/me')->group(function () {
        Route::get('/', [UserController::class, 'profile']);
        Route::get('/history', [UserController::class, 'history']);
        Route::get('/favorites', [UserController::class, 'favorites']);
    });
});

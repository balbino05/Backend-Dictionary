<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DictionaryController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return response()->json(['message' => 'Fullstack Challenge 🏅 - Dictionary']);
});

Route::prefix('auth')->group(function () {
    Route::post('/signup', [AuthController::class, 'register']);
    Route::post('/signin', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:api');
    Route::get('/me', [AuthController::class, 'me'])->middleware('auth:api');
});

Route::prefix('entries/en')->group(function () {
    Route::get('/', [DictionaryController::class, 'index']);
    Route::get('/{word}', [DictionaryController::class, 'show']);
});

Route::middleware('auth:api')->group(function () {
    Route::prefix('entries/en')->group(function () {
        Route::post('/{word}/favorite', [DictionaryController::class, 'favorite']);
        Route::delete('/{word}/unfavorite', [DictionaryController::class, 'unfavorite']);
    });

    Route::prefix('user')->group(function () {
        Route::get('/', [UserController::class, 'getProfile']);
        Route::get('/history', [UserController::class, 'getHistory']);
        Route::get('/favorites', [UserController::class, 'getFavorites']);
    });
});

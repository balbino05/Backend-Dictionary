<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\WordRepositoryInterface;
use App\Repositories\Eloquent\WordRepository;
use App\Services\Contracts\WordServiceInterface;
use App\Services\WordService;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\UserRepository;
use App\Services\Contracts\UserServiceInterface;
use App\Services\UserService;
use App\Repositories\Contracts\HistoryRepositoryInterface;
use App\Repositories\Eloquent\HistoryRepository;
use App\Services\Contracts\HistoryServiceInterface;
use App\Services\HistoryService;
use App\Repositories\Contracts\FavoriteRepositoryInterface;
use App\Repositories\Eloquent\FavoriteRepository;
use App\Services\Contracts\FavoriteServiceInterface;
use App\Services\FavoriteService;
use App\Services\Contracts\AuthServiceInterface;
use App\Services\AuthService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Word
        $this->app->bind(WordRepositoryInterface::class, WordRepository::class);
        $this->app->bind(WordServiceInterface::class, WordService::class);

        // User
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(UserServiceInterface::class, UserService::class);

        // History
        $this->app->bind(HistoryRepositoryInterface::class, HistoryRepository::class);
        $this->app->bind(HistoryServiceInterface::class, HistoryService::class);

        // Favorite
        $this->app->bind(FavoriteRepositoryInterface::class, FavoriteRepository::class);
        $this->app->bind(FavoriteServiceInterface::class, FavoriteService::class);

        $this->app->bind(AuthServiceInterface::class, AuthService::class);

        // Dictionary
        $this->app->bind(\App\Services\Contracts\DictionaryServiceInterface::class, \App\Services\WordService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

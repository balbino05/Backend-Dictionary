<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\WordRepositoryInterface;
use App\Repositories\Eloquent\WordRepository;
use App\Services\Contracts\WordServiceInterface;
use App\Services\WordService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->bind(WordRepositoryInterface::class, WordRepository::class);
        $this->app->bind(WordServiceInterface::class, WordService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        //
    }
}

<?php

namespace App\Providers;

use App\Application\Services\TodoService;
use App\Domain\Repositories\TodoRepositoryInterface;
use App\Infrastructure\Repositories\TodoRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Repository
        $this->app->bind(TodoRepositoryInterface::class, TodoRepository::class);

        // Register Services
        $this->app->bind(TodoService::class, function ($app) {
            return new TodoService($app->make(TodoRepositoryInterface::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

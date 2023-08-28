<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\CartRepositoryInterface;
use App\Repositories\CartRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        $this->app->bind(CartRepositoryInterface::class, CartRepository::class);
    }


    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

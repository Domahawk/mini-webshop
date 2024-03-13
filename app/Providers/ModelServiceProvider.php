<?php

namespace App\Providers;

use App\Services\OrderService;
use App\Services\PriceModifierService;
use App\Services\ProductService;
use Illuminate\Support\ServiceProvider;

class ModelServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ProductService::class, fn () => new ProductService);
        $this->app->singleton(OrderService::class, fn () => new OrderService(
            app(PriceModifierService::class),
            app(ProductService::class),
        ));
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {}
}

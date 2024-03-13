<?php

namespace App\Providers;

use App\Services\PriceModifierService;
use App\Services\ProductFilterSortService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ProductFilterSortService::class, fn () => new ProductFilterSortService);
        $this->app->singleton(PriceModifierService::class, fn () => new PriceModifierService);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

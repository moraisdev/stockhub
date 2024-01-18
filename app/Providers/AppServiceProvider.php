<?php

namespace App\Providers;

use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Models\ProductVariantStock;
use App\Observers\ProductVariantStockObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(UrlGenerator $url)
    {
        Schema::defaultStringLength(191);
        
        if (env('APP_ENV') == 'production') {
            $url->forceScheme('https');
        }

        ProductVariantStock::observe(ProductVariantStockObserver::class);

    }
}

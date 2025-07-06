<?php

namespace App\Providers;

use App\Models\Produk;
use App\Models\Riview;
use App\Observers\ProdukObserver;
use App\Observers\RiviewObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Riview::observe(RiviewObserver::class);
        Produk::observe(ProdukObserver::class);
    }
}

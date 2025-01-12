<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Shop;
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
    public function boot()
    {
        // Chia sẻ biến $shop_get cho view `index`
        View::composer('product.report', function ($view) {
            $shop_get = Shop::select('shop_id', 'shop_name')->get()->toArray();
            $view->with('shop_get', $shop_get);
        });
    }
}

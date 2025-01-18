<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Shop;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
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
        View::composer('header', function ($view) {
            $user = Auth::user();
            if ($user) {
                $userCode = $user->referral_code;
                $transactions = Transaction::where('description', 'LIKE', "%$userCode%")->get();
                $totalAmount = 0;
    
                foreach ($transactions as $transaction) {
                    if ($transaction->type == 'IN') {
                        $totalAmount += $transaction->amount;
                    } 
                }
                $user->total_amount = $totalAmount;
                $user->save();
                $view->with([
                    'user' => $user,
                    'totalAmount' => $totalAmount,
                ]);
            }
        });
    
    }
}

<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const CRM_HOME = '/crm';

    public const ADMIN_HOME = '/admin';

    /**
     * @deprecated Use homeForUser() for role-aware redirects.
     */
    public const HOME = self::CRM_HOME;
    protected $namespace = 'App\\Http\\Controllers';


    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();
    
        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));
    
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
    
            // Tải thêm route từ auth.php
            Route::middleware('web')
                ->group(base_path('routes/auth.php'));
        });
    }
    
    
    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }

    public static function homeForUser(?User $user): string
    {
        if ($user?->hasCrmRole('admin')) {
            return self::ADMIN_HOME;
        }

        return self::CRM_HOME;
    }
}

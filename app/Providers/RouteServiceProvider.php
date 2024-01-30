<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::middleware('web')
                ->group(base_path('routes/Customs/Personal.php'));
            Route::middleware('web')
                ->group(base_path('routes/Customs/Project.php'));
            Route::middleware('web')
                ->group(base_path('routes/Customs/Programming.php'));

            Route::middleware('web')
                ->prefix('security')
                ->group(base_path('routes/Customs/Security.php'));

            Route::middleware('web')
                ->prefix('dashboards')
                ->group(base_path('routes/Customs/Dashboards.php'));

            Route::middleware('web')
                ->prefix('settings')
                ->group(base_path('routes/Customs/Settings.php'));
            Route::middleware('web')
                ->group(base_path('routes/Customs/Quotes.php'));
            Route::middleware('web')
                ->group(base_path('routes/Customs/WareHouse.php'));

            Route::middleware('web')
                ->prefix('documentManagement')
                ->group(base_path('routes/Customs/GestionDocumental.php'));
        });
    }
}

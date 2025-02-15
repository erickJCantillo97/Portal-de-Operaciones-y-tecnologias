<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\Projects\Customer;
use App\Models\Schedule;
use App\Policies\CustomerPolicy;
use App\Policies\SchedulePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Fortify;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Schedule::class => SchedulePolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Fortify::authenticateUsing(function ($request) {
            $validated = Auth::validate([
                'samaccountname' => $request->username,
                'password' => $request->password
            ]);

            return $validated ? Auth::getLastAttempted() : null;
        });
    }
}

<?php

namespace App\Providers;

use App\Http\Controllers\ShiftController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
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
        View::composer('layouts.cashier-template.header', function ($view) {
            $user = Auth::user();

            if (!$user || !$user->hasRole('cashier')) {
                $view->with('headerShiftInfo', null);

                return;
            }

            $view->with('headerShiftInfo', ShiftController::buildHeaderShiftInfo($user->id));
        });
    }
}

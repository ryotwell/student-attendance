<?php

namespace App\Providers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
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
        // if (Auth::user()->role === 'GURU' && !request()->is('teacher-area*')) {
            
        // }

        if(config('app.env') !== 'local') {
            URL::forceScheme('https');
        }

        Carbon::setLocale('id');
    }
}

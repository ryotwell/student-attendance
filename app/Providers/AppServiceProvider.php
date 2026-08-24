<?php

namespace App\Providers;

use App\Helpers\WhatsAppHelper;
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
        if(config('app.env') !== 'local') {
            URL::forceScheme('https');
        }

        Carbon::setLocale('id');

        $this->app->singleton(
            WhatsAppHelper::class,
            function(){
                return new WhatsAppHelper();
            }
        );
    }
}

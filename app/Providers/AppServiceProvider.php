<?php

namespace App\Providers;
use App\Models\SmsGateway;
use App\Observers\SmsGatewayObserver;

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
           SmsGateway::observe(SmsGatewayObserver::class);
           
    }
}

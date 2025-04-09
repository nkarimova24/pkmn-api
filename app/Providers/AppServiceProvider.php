<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\CheckNewSet;
use App\Console\Commands\CheckNewCards;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
        
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schedule::command('check:newsets')->daily();
        Schedule::command('check:newcards')->daily();
    
    }
}

<?php

namespace App\Providers;

use App\Console\Commands\GenerateSenderistaQrToken;
use Illuminate\Support\ServiceProvider;

class ArtisanServiceProvider extends ServiceProvider
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
        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateSenderistaQrToken::class,
            ]);
        }
    }
}

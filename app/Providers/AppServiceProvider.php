<?php

namespace App\Providers;

use App\Pigeon\Parser as PigeonParser;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('pigeon.parser', function () {
            $pigeonParser = new PigeonParser;

            return $pigeonParser;
        });
    }
}

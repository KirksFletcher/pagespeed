<?php

namespace kirksfletcher\pagespeed;

use Illuminate\Support\ServiceProvider;

class PagespeedServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('kirksfletcher\pagespeed\Pagespeed');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}

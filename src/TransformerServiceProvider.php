<?php

namespace CheckerplateSoftware\LaravelTransformer;

use Illuminate\Support\ServiceProvider;

class TransformerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $path = realpath(__DIR__ . '/../config/config.php');
        $this->publishes([$path => config_path('transformer.php')], 'config');
        $this->mergeConfigFrom($path, 'transformer');

        // Expose the 'transform' function
        require realpath(__DIR__ . '/functions.php');
    }
}

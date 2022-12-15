<?php

namespace SoulDoit\ActivityLogger;

use Illuminate\Support\ServiceProvider;

class ActivityLoggerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->offerPublishing();

        $this->app->singleton(Logger::class, function ($app){
            $channel_name = config('sd-activity-logger.channel') ?? config('logging.default');
            return new Logger($channel_name);
        });
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/sd-activity-logger.php',
            'sd-activity-logger'
        );
    }

    protected function offerPublishing()
    {
        if (! function_exists('config_path')) {
            // function not available and 'publish' not relevant in Lumen
            return;
        }

        $this->publishes([
            __DIR__.'/../config/sd-activity-logger.php' => config_path('sd-activity-logger.php'),
        ], 'config');
    }
}
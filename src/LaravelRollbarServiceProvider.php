<?php

namespace Soyhuce\LaravelRollbar;

use Illuminate\Support\ServiceProvider;
use Soyhuce\LaravelRollbar\Facades\Rollbar;

class LaravelRollbarServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(RollbarHandler::class, function ($app) {
            return new RollbarHandler(
                Rollbar::logger(),
                $app->get('config')->get('logging.channels.rollbar.level', 'debug'),
            );
        });
    }

    public function boot(): void
    {
        // Register exception, fatal and error handlers ?
    }
}

<?php

namespace Soyhuce\LaravelRollbar;

use Illuminate\Support\ServiceProvider;
use Soyhuce\LaravelRollbar\Facades\Rollbar;

class LaravelRollbarServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Workaround that mus be fixed
        ini_set('zend.exception_ignore_args', '');

        $this->app->bind(LaravelRollbarHandler::class, function ($app) {
            return new LaravelRollbarHandler(
                Rollbar::logger(),
                $app->get('config')->get('logging.channels.rollbar.level', 'debug'),
            );
        });
    }
}

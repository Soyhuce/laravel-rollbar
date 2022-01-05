<?php

namespace Soyhuce\LaravelRollbar\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Soyhuce\LaravelRollbar\LaravelRollbarServiceProvider;

/**
 * @coversNothing
 */
class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            LaravelRollbarServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app->get('config')->set(
            'logging.channels.rollbar.access_token',
            'abcdefghijklmnopqrstuvwxyz012345'
        );
    }
}

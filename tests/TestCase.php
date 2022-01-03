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
}

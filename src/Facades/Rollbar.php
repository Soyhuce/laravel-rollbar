<?php

namespace Soyhuce\LaravelRollbar\Facades;

use Illuminate\Support\Facades\Facade;
use Soyhuce\LaravelRollbar\Fakes\FakeRollbar;
use Soyhuce\LaravelRollbar\LaravelRollbar;

/**
 * @method static \Rollbar\RollbarLogger logger()
 * @method static void addPersonContextToLogger()
 * @method static void resolveAuthenticatedUserUsing(callable $resolver)
 * @method static void assertConfig(array $expected)
 * @method static void assertPersonContext(array $expected)
 * @method static void assertLogged(callable $callback)
 *
 * @see \Soyhuce\LaravelRollbar\LaravelRollbar
 */
class Rollbar extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return LaravelRollbar::class;
    }

    public static function fake(): FakeRollbar
    {
        static::swap($fake = static::$app->make(FakeRollbar::class));

        return $fake;
    }
}

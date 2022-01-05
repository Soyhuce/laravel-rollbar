<?php

namespace Soyhuce\LaravelRollbar;

use Monolog\Handler\RollbarHandler as MonologRollbarHandler;
use Soyhuce\LaravelRollbar\Facades\Rollbar;

class LaravelRollbarHandler extends MonologRollbarHandler
{
    protected function write(array $record): void
    {
        Rollbar::addPersonContextToLogger();
        parent::write($record);
    }
}

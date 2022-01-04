<?php

namespace Soyhuce\LaravelRollbar;

use Monolog\Handler\RollbarHandler as MonologRollbarHandler;
use Soyhuce\LaravelRollbar\Facades\Rollbar as RollbarFacade;

class RollbarHandler extends MonologRollbarHandler
{
    protected function write(array $record): void
    {
        RollbarFacade::addPersonContextToLogger();
        parent::write($record);
    }
}

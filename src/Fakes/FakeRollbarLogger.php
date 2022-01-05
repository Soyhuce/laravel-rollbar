<?php

namespace Soyhuce\LaravelRollbar\Fakes;

use Illuminate\Support\Collection;
use Rollbar\Response;
use Rollbar\RollbarLogger;

class FakeRollbarLogger extends RollbarLogger
{
    /** @var array<\Rollbar\Payload\Data> */
    public array $logs = [];

    /**
     * @param string $level
     * @param string|\Throwable $toLog
     * @param array<string, mixed> $context
     */
    public function log($level, $toLog, array $context = []): Response
    {
        $this->logs[] = $this->getPayload($this->getAccessToken(), $level, $toLog, $context)->getData();

        return new Response(200, 'OK');
    }

    /**
     * @return \Illuminate\Support\Collection<\Rollbar\Payload\Data>
     */
    public function logs(): Collection
    {
        return new Collection($this->logs);
    }
}

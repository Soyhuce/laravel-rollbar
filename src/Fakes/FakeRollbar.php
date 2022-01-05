<?php

namespace Soyhuce\LaravelRollbar\Fakes;

use PHPUnit\Framework\Assert;
use Rollbar\Payload\Data;
use Soyhuce\LaravelRollbar\LaravelRollbar;

/**
 * @property \Soyhuce\LaravelRollbar\Fakes\FakeRollbarLogger $logger
 */
class FakeRollbar extends LaravelRollbar
{
    protected function initialize(): void
    {
        $this->logger = new FakeRollbarLogger($this->loggerConfig());
    }

    /**
     * @param array<string, mixed> $expected
     */
    public function assertConfig(array $expected): void
    {
        Assert::assertEquals($expected, $this->loggerConfig());
    }

    /**
     * @param array<string, mixed> $expected
     */
    public function assertPersonContext(array $expected): void
    {
        Assert::assertEquals($expected, $this->resolvePersonContext());
    }

    /**
     * @param callable(\Rollbar\Payload\Data): bool $callback
     */
    public function assertLogged(callable $callback): void
    {
        $logs = $this->logger->logs()->filter($callback);

        Assert::assertNotEmpty(
            $logs,
            'Failed to assert that the message was found within the logs.' . PHP_EOL
            . 'Current logs :' . PHP_EOL .
            json_encode($this->logger->logs()->map(fn (Data $data) => $data->serialize()), JSON_PRETTY_PRINT)
        );
    }
}

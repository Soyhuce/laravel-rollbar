<?php

namespace Soyhuce\LaravelRollbar;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Rollbar\Handlers\ErrorHandler;
use Rollbar\Handlers\ExceptionHandler;
use Rollbar\Handlers\FatalHandler;
use Rollbar\RollbarLogger;

class LaravelRollbar
{
    protected RollbarLogger $logger;

    /** @var array<\Rollbar\Handlers\AbstractHandler> */
    protected array $handlers;

    /** @var null|callable(): array<string, mixed> */
    protected $authenticatedUserResolver = null;

    public function __construct(
        protected Application $app,
        protected Repository $config,
    ) {
        $this->initialize();
    }

    public function logger(): RollbarLogger
    {
        return $this->logger;
    }

    protected function initialize(): void
    {
        $this->logger = new RollbarLogger($this->loggerConfig());
        $this->handlers = [
            new ExceptionHandler($this->logger),
            new ErrorHandler($this->logger),
            new FatalHandler($this->logger),
        ];
        foreach ($this->handlers as $handler) {
            $handler->register();
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function loggerConfig(): array
    {
        $config = collect($this->config->get('logging.channels.rollbar'));

        if (!$config->has('access_token')) {
            $config->put('access_token', 'access_token is missing in conf.');
        }

        return $config
            ->except(['driver', 'handler', 'level'])
            ->merge([
                'environment' => $this->app->environment(),
                'root' => $this->app->basePath(),
            ])
            ->all();
    }

    public function addPersonContextToLogger(): void
    {
        $this->logger->configure(['person' => $this->resolvePersonContext()]);
    }

    /**
     * @param callable(): array<string, mixed> $resolver
     */
    public function resolveAuthenticatedUserUsing(callable $resolver): void
    {
        $this->authenticatedUserResolver = $resolver;
    }

    /**
     * @return array<string, mixed>
     */
    protected function resolvePersonContext(): array
    {
        $context = [];
        if ($this->app->has('session') && $this->app->get('session')->isStarted()) {
            $context = [
                'session' => $this->app->get('session')->all(),
                'id' => $this->app->get('session')->getId(),
            ];
        }

        return array_merge($context, $this->resolveAuthenticatedUser());
    }

    /**
     * @return array<string, mixed>
     */
    protected function resolveAuthenticatedUser(): array
    {
        $resolver = $this->authenticatedUserResolver
            ?? fn () => $this->app->get('auth')->check()
                ? ['id' => (string) $this->app->get('auth')->id()]
                : [];

        return $resolver();
    }
}

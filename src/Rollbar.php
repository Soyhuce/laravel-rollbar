<?php

namespace Soyhuce\LaravelRollbar;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Rollbar\RollbarLogger;

class Rollbar
{
    protected RollbarLogger $logger;

    public function __construct(
        protected Application $app,
        protected Repository $config,
    ) {
        $this->initialize();
        // handle exceptions, errors and fatals
    }

    public function logger(): RollbarLogger
    {
        return $this->logger;
    }

    protected function initialize(): void
    {
        $this->logger = new RollbarLogger($this->loggerConfig());
    }

    /**
     * @return array<string, mixed>
     */
    protected function loggerConfig(): array
    {
        return collect($this->config->get('logging.channels.rollbar'))
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

        // TODO : make it configurable
        if ($this->app->get('auth')->check()) {
            $context['id'] = (string) $this->app->get('auth')->id();
        }

        return $context;
    }
}

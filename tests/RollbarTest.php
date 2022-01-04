<?php

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Rollbar\Payload\Data;
use Soyhuce\LaravelRollbar\Facades\Rollbar;
use Soyhuce\LaravelRollbar\RollbarHandler;

beforeEach(function (): void {
    $this->app->get('config')->set([
        'logging.default' => 'rollbar',
        'logging.channels.rollbar' => [
            'driver' => 'monolog',
            'handler' => RollbarHandler::class,
            'access_token' => 'abcdefghijklmnopqrstuvwxyz012345',
            'level' => 'debug',
        ],
    ]);

    Rollbar::fake();
});

it('loads the config', function (): void {
    Rollbar::assertConfig([
        'access_token' => 'abcdefghijklmnopqrstuvwxyz012345',
        'environment' => 'testing',
        'root' => base_path(),
    ]);
});

it('resolves current user id', function (): void {
    User::unguard();
    Auth::setUser(new User(['id' => 1]));

    Rollbar::assertPersonContext(['id' => 1]);
});

it('resolves current session data', function (): void {
    $session = $this->app->get('session');
    $session->start();
    $session->put('foo', 'bar');

    Rollbar::assertPersonContext([
        'id' => $session->getId(),
        'session' => [
            'foo' => 'bar',
            '_token' => $session->token(),
        ],
    ]);
});

it('logs a message', function (): void {
    Log::debug('The message');

    Rollbar::assertLogged(
        fn (Data $message) => $message->getLevel()->serialize() === 'debug'
            && $message->getBody()->getValue()->serialize()['body'] === 'The message'
            && $message->getBody()->getExtra()['level'] === 'debug'
            && $message->getBody()->getExtra()['channel'] === 'testing'
    );
});

it('logs a message with current user id', function (): void {
    User::unguard();
    Auth::setUser(new User(['id' => 1]));
    Log::debug('The message');

    Rollbar::assertLogged(fn (Data $message) => $message->getPerson()->getId() === '1');
});

it('logs a message with current session', function (): void {
    $session = $this->app->get('session');
    $session->start();
    $session->put('foo', 'bar');
    Log::debug('The message');

    Rollbar::assertLogged(
        fn (Data $message) => $message->getPerson()->getId() === $session->getId()
            && $message->getPerson()->serialize()['session']['foo'] === 'bar'
    );
});

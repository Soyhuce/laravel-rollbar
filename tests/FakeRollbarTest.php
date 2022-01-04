<?php

use Illuminate\Config\Repository;
use Illuminate\Support\Arr;
use PHPUnit\Framework\ExpectationFailedException;
use Rollbar\Payload\Data;
use Soyhuce\LaravelRollbar\Fakes\FakeRollbar;

beforeEach(function (): void {
    $repository = new Repository(Arr::undot([
        'logging.channels.rollbar' => [
            'access_token' => '012345678901234567890123456798ab',
        ],
    ]));

    $this->fakeRollbar = new FakeRollbar($this->app, $repository);
});

it('can check the config', function (): void {
    $this->fakeRollbar->assertConfig([
        'access_token' => '012345678901234567890123456798ab',
        'environment' => 'testing',
        'root' => base_path(),
    ]);
});

it('can fail to check the config', function (): void {
    $this->expectException(ExpectationFailedException::class);
    $this->fakeRollbar->assertConfig([
        'foo' => 'baz',
        'environment' => 'testing',
        'root' => base_path(),
    ]);
});

it('can check the person context', function (): void {
    $this->fakeRollbar->assertPersonContext([]);
});

it('can fail to check the person context', function (): void {
    $this->expectException(ExpectationFailedException::class);
    $this->fakeRollbar->assertPersonContext(['id' => 1]);
});

it('can check a message was logged', function (): void {
    $this->fakeRollbar->logger()->log('debug', 'The message');

    $this->fakeRollbar->assertLogged(
        fn (Data $data) => $data->getLevel()->serialize() === 'debug'
            && $data->getBody()->getValue()->serialize()['body'] === 'The message'
    );
});

it('can fail to check a message was logged', function (): void {
    $this->expectException(ExpectationFailedException::class);
    $this->fakeRollbar->assertLogged(
        fn (Data $data) => $data->getLevel()->serialize() === 'debug'
            && $data->getBody()->getValue()->serialize()['body'] === 'The message'
    );
});

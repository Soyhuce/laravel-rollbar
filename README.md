![alt text](https://soyhuce.fr/wp-content/uploads/2020/06/logo-soyhuce-dark-1.png "Soyhuce")

# Rollbar wrapper for Laravel

## Installation

You can install the package via composer:

```bash
composer require soyhuce/laravel-rollbar
```

You can then add rollbar configuration in your `config/logging.php`

```php
return [
    //...

    'channels' => [
        'rollbar' => [
            'driver' => 'monolog',
            'handler' => \Soyhuce\LaravelRollbar\LaravelRollbarHandler::class,
            'level' => env('LOG_LEVEL', 'error'),
            'access_token' => env('ROLLBAR_SERVER_KEY'),
        ],
    ],
];
```

You can add the configurations you need. See
the [Rollbar documentation](https://docs.rollbar.com/docs/php-configuration-reference) for more information.

### Customizing current user resolution

You can customize the current user resolution
with `\Soyhuce\LaravelRollbar\Facades\Rollbar::resolveAuthenticatedUserUsing()`. You may want to add this in a service
provider (for example `AppServiceProvider`).

```php
\Soyhuce\LaravelRollbar\Facades\Rollbar::resolveAuthenticatedUserUsing(function (): array {
    $user = auth()->user();
    
    if ($user === null) {
        return [];
    }
    
    return [
        'id' => (string) $user->id, // id must be a string
        'role' => $user->role->label,
    ];
});
```

A default user resolver is provided in this package.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

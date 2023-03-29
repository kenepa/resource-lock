# Resoure Lock

[![Latest Version on Packagist](https://img.shields.io/packagist/v/kenepa/resourcelock.svg?style=flat-square)](https://packagist.org/packages/kenepa/resourcelock)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/kenepa/resourcelock/run-tests?label=tests)](https://github.com/kenepa/resourcelock/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/kenepa/resourcelock/Check%20&%20fix%20styling?label=code%20style)](https://github.com/kenepa/resourcelock/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/kenepa/resourcelock.svg?style=flat-square)](https://packagist.org/packages/kenepa/resourcelock)



This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require kenepa/resource-lock
```

You can run the install command to publish config files, migrations and run migrations (optional)

```bash
php artisan resource-lock:instal
```

```bash
php artisan vendor:publish --tag="resource-lock-migrations"
php artisan migrate
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="resource-lock-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="resource-lock-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="resource-lock-views"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$resourcelock = new Kenepa\ResourceLock();
echo $resourcelock->echoPhrase('Hello, Kenepa!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Jehizkia](https://github.com/Jehizkia)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

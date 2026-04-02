# This is my package filament-oh-dear

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ziming/filament-oh-dear.svg?style=flat-square)](https://packagist.org/packages/ziming/filament-oh-dear)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/ziming/filament-oh-dear/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/ziming/filament-oh-dear/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/ziming/filament-oh-dear/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/ziming/filament-oh-dear/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/ziming/filament-oh-dear.svg?style=flat-square)](https://packagist.org/packages/ziming/filament-oh-dear)

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Support us

To be added

## Installation

You can install the package via composer:

```bash
composer require ziming/filament-oh-dear
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="filament-oh-dear-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-oh-dear-config"
```

This is the contents of the published config file:

```php
return [
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="filament-oh-dear-views"
```

## Usage

```php
$filamentOhDear = new Ziming\FilamentOhDear();
echo $filamentOhDear->echoPhrase('Hello, Ziming!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [ziming](https://github.com/ziming)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

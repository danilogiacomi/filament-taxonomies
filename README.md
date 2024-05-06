# A Filament plugin to manage taxonomies

[![Latest Version on Packagist](https://img.shields.io/packagist/v/net7/filament-taxonomies.svg?style=flat-square)](https://packagist.org/packages/net7/filament-taxonomies)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/net7/filament-taxonomies/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/net7/filament-taxonomies/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/net7/filament-taxonomies/fix-php-code-styling.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/net7/filament-taxonomies/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/net7/filament-taxonomies.svg?style=flat-square)](https://packagist.org/packages/net7/filament-taxonomies)


A Filament plugin to manage multi-level taxonomies 

## Installation

You can install the package via composer:

```bash
composer require net7/filament-taxonomies
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="filament-taxonomies-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-taxonomies-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="filament-taxonomies-views"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$filamentTaxonomies = new Net7\FilamentTaxonomies();
echo $filamentTaxonomies->echoPhrase('Hello, net7!');
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

- [Danilo Giacomi](https://github.com/danilogiacomi)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

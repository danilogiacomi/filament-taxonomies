# Filament Taxonomies

[![Latest Version on Packagist](https://img.shields.io/packagist/v/net7/filament-taxonomies.svg?style=flat-square)](https://packagist.org/packages/net7/filament-taxonomies)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/net7/filament-taxonomies/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/net7/filament-taxonomies/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/net7/filament-taxonomies/fix-php-code-styling.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/net7/filament-taxonomies/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/net7/filament-taxonomies.svg?style=flat-square)](https://packagist.org/packages/net7/filament-taxonomies)


A comprehensive Filament plugin for managing hierarchical taxonomies and terms with semantic metadata support. Perfect for organizing content with multiple classification systems including categories, tags, and controlled vocabularies.

## Features

- **Hierarchical Taxonomies**: Create and manage multi-level taxonomy structures
- **Flexible Terms**: Support for parent-child relationships between terms
- **Semantic Metadata**: Built-in URI management with internal auto-generation and external URI support
- **Multiple Taxonomy Types**: Public, restricted, and private taxonomy classifications
- **State Management**: Working and published states for content lifecycle management
- **Unique Constraints**: Automatic validation to prevent duplicate taxonomy names
- **Bulk Operations**: Efficient management of large taxonomy datasets 

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

## Usage

### Basic Usage

Once installed, the plugin will automatically register two main resources in your Filament admin panel:

- **Taxonomies**: Manage your classification systems
- **Terms**: Manage individual terms within taxonomies

### Working with Taxonomies

```php
use Net7\FilamentTaxonomies\Models\Taxonomy;
use Net7\FilamentTaxonomies\Enums\TaxonomyStates;
use Net7\FilamentTaxonomies\Enums\TaxonomyTypes;

// Create a new taxonomy
$taxonomy = Taxonomy::create([
    'name' => 'Blog Categories',
    'description' => 'Categories for blog posts',
    'state' => TaxonomyStates::published,
    'type' => TaxonomyTypes::public,
]);

// The URI is automatically generated
echo $taxonomy->uri; // http://yourapp.com/taxonomies/blog-categories
```

### Working with Terms

```php
use Net7\FilamentTaxonomies\Models\Term;
use Net7\FilamentTaxonomies\Enums\UriTypes;

// Create a root term
$parentTerm = Term::create([
    'name' => 'Technology',
    'description' => 'Technology related content',
    'uri_type' => UriTypes::internal,
]);

// Create a child term
$childTerm = Term::create([
    'name' => 'Web Development',
    'description' => 'Web development topics',
    'parent_id' => $parentTerm->id,
    'uri_type' => UriTypes::internal,
]);

// Associate terms with taxonomies
$taxonomy->terms()->attach([$parentTerm->id, $childTerm->id]);
```

### Semantic Metadata Management

The plugin provides advanced semantic metadata management through dedicated actions:

- **Internal URIs**: Automatically generated based on term names
- **External URIs**: Custom URIs with domain validation
- **Exact Match URIs**: Additional semantic references

### Available Enums

#### TaxonomyStates
- `working`: Taxonomy is being developed
- `published`: Taxonomy is live and available

#### TaxonomyTypes
- `public`: Accessible to all users
- `restricted`: Limited access
- `private`: Internal use only

#### UriTypes
- `internal`: Auto-generated internal URIs
- `external`: Custom external URIs

### Seeders

The package includes seeders for development and testing:

```bash
php artisan db:seed --class="Net7\FilamentTaxonomies\Database\Seeders\TaxonomySeeder"
php artisan db:seed --class="Net7\FilamentTaxonomies\Database\Seeders\TermSeeder"
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
- [Alessandro Bertozzi](https://github.com/0xAbe42)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

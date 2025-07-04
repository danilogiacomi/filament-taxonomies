# Filament Taxonomies

[![Latest Version on Packagist](https://img.shields.io/packagist/v/net7/filament-taxonomies.svg?style=flat-square)](https://packagist.org/packages/net7/filament-taxonomies)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/net7/filament-taxonomies/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/net7/filament-taxonomies/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/net7/filament-taxonomies/fix-php-code-styling.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/net7/filament-taxonomies/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/net7/filament-taxonomies.svg?style=flat-square)](https://packagist.org/packages/net7/filament-taxonomies)


A comprehensive Filament plugin for managing hierarchical taxonomies and terms with advanced features including slug-based operations, level filtering, and semantic metadata support. Perfect for organizing content with multiple classification systems including categories, tags, and controlled vocabularies with enterprise-grade reliability.

## Features

- **Hierarchical Taxonomies**: Create and manage multi-level taxonomy structures with parent-child relationships
- **Flexible Terms**: Support for unlimited hierarchy depth with built-in 10-level protection
- **Level Filtering**: Advanced filtering system to show terms by specific hierarchy levels
- **Slug-Based Operations**: URL-friendly slugs for reliable, case-insensitive operations
- **Auto-Generated Slugs**: Automatic slug generation from names for both taxonomies and terms
- **Semantic Metadata**: Built-in URI management with internal auto-generation and external URI support
- **Multiple Taxonomy Types**: Public, restricted, and private taxonomy classifications
- **State Management**: Working and published states for content lifecycle management
- **Polymorphic Relationships**: Works with any Laravel model via the HasTaxonomies trait
- **Foreign Key Integrity**: Proper database relationships with cascade delete protection
- **Unique Constraints**: Automatic validation to prevent duplicate taxonomy names and slugs
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

### Using the TaxonomySelect Component

The plugin includes a powerful Filament form component with advanced level filtering and slug-based operations:

```php
use Net7\FilamentTaxonomies\Forms\Components\TaxonomySelect;

// Basic usage (uses taxonomy slug for reliable operations)
TaxonomySelect::make('categories')
    ->taxonomy('product-categories') // Slug-based (case-insensitive, reliable)
    ->multiple(),

// Root level only (level 0)
TaxonomySelect::make('main_category')
    ->taxonomy('product-categories')
    ->rootLevel(),

// Specific level only
TaxonomySelect::make('subcategories')
    ->taxonomy('product-categories')
    ->exactLevel(1),

// Maximum level filtering (up to level 2)
TaxonomySelect::make('categories')
    ->taxonomy('product-categories')
    ->maxLevel(2)
    ->multiple(),

// Minimum level filtering (level 2 and deeper)
TaxonomySelect::make('detailed_tags')
    ->taxonomy('product-categories')
    ->minLevel(2)
    ->multiple(),

// Range filtering (levels 1-3)
TaxonomySelect::make('mid_level_categories')
    ->taxonomy('product-categories')
    ->minLevel(1)
    ->maxLevel(3)
    ->multiple(),

// Sub-levels - two selects where one is used to choose the first level item, then the second select
// is forced to choose among sub-items of the one chosen in the first

TaxonomySelect::make('categories')
    ->taxonomy('product-categories')
    ->afterStateUpdated(function ($state, callable $set) {
        if ($state) {
            $set('sub-categories', null);
        }
    })
    ->reactive(),

TaxonomySelect::make('sub-categories')
    ->taxonomy('product-categories')
    ->parentItemFrom('categories')
    ->disabled(fn ($get) => ! $get('categories')),
```

### Slug-Based Operations

All operations now use slugs for reliability while maintaining user-friendly names in the interface:

- **Operations**: Use taxonomy slugs (e.g., `'product-categories'`)
- **Display**: Show human-readable names (e.g., "Product Categories")
- **Benefits**: Case-insensitive, URL-safe, no special character issues

### Hierarchy Level Management

The plugin enforces a maximum hierarchy depth of **10 levels** to ensure performance and prevent infinite loops:

- **Level 0**: Root terms (no parent)
- **Level 1**: Direct children of root terms
- **Level 2**: Grandchildren of root terms
- **Level 3-10**: Deeper nested terms

The system prevents creating terms beyond level 10 through:
- Database-level validation in the Term model
- UI-level filtering in Filament admin panels
- Component-level validation in TaxonomySelect

### Real-World Usage Example

```php
// In a Filament Resource form
use Net7\FilamentTaxonomies\Forms\Components\TaxonomySelect;

public static function form(Form $form): Form
{
    return $form
        ->schema([
            TextInput::make('name')->required(),
            
            // Main category (root level only)
            TaxonomySelect::make('main_category')
                ->taxonomy('Product Categories')
                ->rootLevel()
                ->label('Main Category')
                ->required(),
            
            // Subcategories (level 1 only)
            TaxonomySelect::make('subcategories')
                ->taxonomy('Product Categories')
                ->exactLevel(1)
                ->multiple()
                ->label('Subcategories'),
            
            // Detailed tags (level 2 and deeper)
            TaxonomySelect::make('tags')
                ->taxonomy('Product Tags')
                ->minLevel(2)
                ->multiple()
                ->label('Detailed Tags'),
        ]);
}
```

### Working with Models

The `HasTaxonomies` trait provides multiple approaches for different use cases:

```php
use Net7\FilamentTaxonomies\Traits\HasTaxonomies;

class Product extends Model
{
    use HasTaxonomies;
}

// Recommended approach (using taxonomy slug - reliable & case-insensitive)
$product->setTermsForTaxonomySlug('product-categories', [1, 2, 3]);
$terms = $product->getTermsForTaxonomySlug('product-categories');
$hasTag = $product->hasTermInTaxonomySlug('product-categories', 2);

// Alternative approach (using taxonomy ID - most efficient)
$product->setTermsForTaxonomyId(1, [1, 2, 3]);
$terms = $product->getTermsForTaxonomyId(1);
$hasTag = $product->hasTermInTaxonomyId(1, 2);

// Legacy support (using taxonomy name - deprecated)
$product->setTermsForTaxonomy('Product Categories', [1, 2, 3]);
$terms = $product->getTermsForTaxonomy('Product Categories');
$hasTag = $product->hasTermInTaxonomy('Product Categories', 2);
```

### Database Schema

The plugin creates an optimized database structure with proper relationships:

#### Tables
- **`taxonomies`**: Main taxonomy definitions with auto-generated slugs
- **`terms`**: Hierarchical terms with parent-child relationships and auto-generated slugs  
- **`taxonomy_term`**: Many-to-many pivot between taxonomies and terms
- **`entity_terms`**: Polymorphic relationships between any model and terms

#### Key Features
- **Foreign Key Constraints**: Proper referential integrity with cascade deletes
- **Unique Constraints**: Prevents duplicate names and slugs
- **Indexes**: Optimized for fast queries on taxonomy_id and entity relationships
- **Polymorphic Support**: Works with any Eloquent model

### Auto-Generated Slugs

Both taxonomies and terms automatically generate URL-friendly slugs:

- **Taxonomies**: `"Product Categories"` → `"product-categories"`
- **Terms**: `"Web Development"` → `"web-development"`
- **Features**: Automatic generation, unique constraints, read-only in admin interface
- **Benefits**: SEO-friendly URLs, case-insensitive operations, special character handling

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

## Recent Improvements & New Features

### v2.0 Updates

#### 🔥 Slug-Based Operations
- **Auto-generated slugs** for both taxonomies and terms
- **Case-insensitive operations** using slugs instead of names
- **URL-friendly identifiers** for better SEO and API usage
- **Backward compatibility** maintained with name-based methods

#### 🎯 Advanced Level Filtering
- **Exact level selection**: `exactLevel(1)` for specific hierarchy levels
- **Range filtering**: `minLevel(1)->maxLevel(3)` for level ranges
- **Root level shortcuts**: `rootLevel()` for top-level terms only
- **Maximum depth protection**: Built-in 10-level hierarchy limit

#### 🏗️ Enhanced Database Structure
- **Foreign key constraints** with proper referential integrity
- **Taxonomy ID references** instead of names for better performance
- **Optimized indexes** for faster queries
- **Cascade delete protection** for data consistency

#### 🛡️ Enterprise Features
- **Hierarchy validation** prevents infinite loops and deep nesting
- **Unique constraints** on both names and slugs
- **Polymorphic relationships** work with any Laravel model
- **Admin interface protection** with disabled slug editing

#### 📊 Developer Experience
- **Three operation methods**: By ID (fastest), by slug (recommended), by name (legacy)
- **Real-time slug generation** in admin interface
- **Clear deprecation notices** for migration guidance
- **Comprehensive documentation** with practical examples

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

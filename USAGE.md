# Filament Taxonomies - Advanced Usage

## TaxonomySelect Component

A reusable Filament form component that automatically handles taxonomy field relationships.

### Basic Usage

```php
use Net7\FilamentTaxonomies\Forms\Components\TaxonomySelect;

// In your Filament Resource form
TaxonomySelect::make('complexity')
    ->taxonomy('complexity')
    ->label('Project Complexity')
```

### Features

- **Automatic data loading**: Loads terms from the specified taxonomy
- **Auto-save**: Automatically saves selected terms to polymorphic `entity_terms` table
- **Zero configuration**: No need for custom save/load logic in Resource pages
- **Polymorphic**: Works with any model that uses the `HasTaxonomies` trait

## HasTaxonomies Trait

Add taxonomy support to any Eloquent model.

### Setup

```php
use Net7\FilamentTaxonomies\Traits\HasTaxonomies;

class Project extends Model
{
    use HasTaxonomies;
    
    // Your model code...
}
```

### Available Methods

```php
// Get all terms for a specific taxonomy
$project->getTermsForTaxonomy('complexity');

// Set terms for a taxonomy (replaces existing)
$project->setTermsForTaxonomy('complexity', [1, 2, 3]);

// Check if model has specific term
$project->hasTermInTaxonomy('complexity', 1);

// Get the polymorphic relationship
$project->entityTerms();
```

## Database Structure

The package uses a polymorphic `entity_terms` table:

```sql
entity_terms:
- id
- entity_type (App\Models\Project)
- entity_id (1) 
- taxonomy_type (complexity)
- term_id (3)
- timestamps
```

## Complete Example

### 1. Model Setup
```php
class Project extends Model
{
    use HasTaxonomies;
    
    protected $fillable = ['name', 'description'];
}
```

### 2. Filament Resource
```php
use Net7\FilamentTaxonomies\Forms\Components\TaxonomySelect;

class ProjectResource extends Resource
{
    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->required(),
            
            TaxonomySelect::make('complexity')
                ->taxonomy('complexity')
                ->label('Project Complexity'),
                
            TaxonomySelect::make('department')
                ->taxonomy('department')
                ->label('Responsible Department'),
        ]);
    }
}
```

### 3. That's it!

No additional configuration needed in Resource pages. The component handles everything automatically.

## Migration

Make sure to run the package migrations:

```bash
php artisan migrate
```

This creates both the `taxonomies`, `terms`, and `entity_terms` tables.
<?php

namespace Net7\FilamentTaxonomies\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Net7\FilamentTaxonomies\Database\Factories\TaxonomyFactory;
use Net7\FilamentTaxonomies\Enums\TaxonomyStates;
use Net7\FilamentTaxonomies\Enums\TaxonomyTypes;

class Taxonomy extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'taxonomies';

    protected $guarded = ['id'];

    protected $fillable = ['name', 'slug', 'description', 'state', 'type', 'uri'];

    protected $casts = [
        'state' => TaxonomyStates::class,
        'type' => TaxonomyTypes::class,
    ];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function terms()
    {
        return $this->belongsToMany(Term::class, 'taxonomy_term')->withTimestamps();
    }

    protected static function newFactory()
    {
        return TaxonomyFactory::new();
    }

    protected static function booted()
    {
        static::creating(function (Taxonomy $taxonomy) {
            if (empty($taxonomy->slug)) {
                $taxonomy->slug = Str::slug($taxonomy->name);
            }
            if (empty($taxonomy->uri)) {
                $taxonomy->uri = $taxonomy->generateInternalUri();
            }
        });

        static::updating(function (Taxonomy $taxonomy) {
            if ($taxonomy->isDirty('name')) {
                $taxonomy->slug = Str::slug($taxonomy->name);
                $taxonomy->uri = $taxonomy->generateInternalUri();
            }
        });
    }

    public function generateInternalUri(): string
    {
        $baseUrl = rtrim(env('APP_URL', 'http://localhost'), '/');
        $taxonomySlug = Str::slug($this->name);

        return "{$baseUrl}/{$taxonomySlug}";
    }
    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}

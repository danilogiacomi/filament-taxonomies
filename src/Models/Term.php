<?php

namespace Net7\FilamentTaxonomies\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Net7\FilamentTaxonomies\Database\Factories\TermFactory;
use Net7\FilamentTaxonomies\Enums\UriTypes;

class Term extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    public const MAX_HIERARCHY_LEVEL = 10;

    protected $table = 'terms';

    protected $guarded = ['id'];

    protected $fillable = ['name', 'slug', 'description', 'parent_id', 'uri', 'uri_type', 'exact_match_uri', 'aliases'];

    protected $casts = [
        'aliases' => 'array',
        'uri_type' => UriTypes::class,
    ];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    public function calculateLevel(): int
    {
        $level = 0;
        $currentTerm = $this;

        while ($currentTerm->parent_id) {
            $level++;
            $currentTerm = $currentTerm->parent;

            // Safety check to prevent infinite loops
            if ($level > self::MAX_HIERARCHY_LEVEL) {
                break;
            }
        }

        return $level;
    }

    public function validateHierarchyLevel(): bool
    {
        if (! $this->parent_id) {
            return true; // Root level is always valid
        }

        $parentLevel = $this->parent->calculateLevel();

        return ($parentLevel + 1) <= self::MAX_HIERARCHY_LEVEL;
    }

    public static function findByTaxonomyIdAndNameOrSlugOrAlias(int $taxonomyId, string $nameOrAlias): ?self
    {
        return
            $this->whereHas('taxonomies', function ($query) use ($taxonomyId) {
                $query->where('taxonomies.id', $taxonomyId);
            })
                ->where('name', $nameOrAlias)
                ->orWhere('slug', $nameOrAlias)
                ->orWhereJsonContains('alias', $nameOrAlias)
                ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function parent()
    {
        return $this->belongsTo(Term::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Term::class, 'parent_id');
    }

    public function taxonomies()
    {
        return $this->belongsToMany(Taxonomy::class, 'taxonomy_term')->withTimestamps();
    }

    protected static function newFactory()
    {
        return TermFactory::new();
    }

    protected static function booted()
    {
        static::creating(function (Term $term) {
            if (empty($term->slug)) {
                $term->slug = Str::slug($term->name);
            }
            if ($term->uri_type === UriTypes::internal || empty($term->uri)) {
                $term->uri_type = UriTypes::internal;
                $term->uri = $term->generateInternalUri();
            }
        });

        static::updating(function (Term $term) {
            if ($term->isDirty('name')) {
                $term->slug = Str::slug($term->name);
            }
            if ($term->isDirty('name') && $term->uri_type === UriTypes::internal) {
                $term->uri = $term->generateInternalUri();
            }
        });

        static::saving(function (Term $term) {
            if ($term->parent_id && ! $term->validateHierarchyLevel()) {
                throw new \InvalidArgumentException(
                    'Term hierarchy cannot exceed '.self::MAX_HIERARCHY_LEVEL.' levels'
                );
            }
        });
    }

    public function validateUniqueNameInTaxonomies(): bool
    {
        foreach ($this->taxonomies as $taxonomy) {
            $existingTerm = $taxonomy->terms()
                ->where('name', $this->name)
                ->where('id', '!=', $this->id)
                ->first();

            if ($existingTerm) {
                return false;
            }
        }

        return true;
    }

    public function generateInternalUri(): string
    {
        $baseUrl = rtrim(env('APP_URL', 'http://localhost'), '/') . '/taxonomies';
        $termSlug = Str::slug($this->name);

        $taxonomy = $this->taxonomies()->first();
        if ($taxonomy) {
            $taxonomySlug = Str::slug($taxonomy->name);

            return "{$baseUrl}/{$taxonomySlug}/{$termSlug}";
        }

        return "{$baseUrl}/terms/{$termSlug}";
    }

    public function isExternalUri(): bool
    {
        return $this->uri_type === UriTypes::external;
    }

    public function validateExternalUri(): bool
    {
        if ($this->uri_type !== UriTypes::external) {
            return true;
        }

        $appUrl = env('APP_URL', 'http://localhost');
        $appDomain = parse_url($appUrl, PHP_URL_HOST);
        $uriDomain = parse_url($this->uri, PHP_URL_HOST);

        // If we can't parse the domains, assume it's valid
        if (! $appDomain || ! $uriDomain) {
            return true;
        }

        return $uriDomain !== $appDomain;
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

}

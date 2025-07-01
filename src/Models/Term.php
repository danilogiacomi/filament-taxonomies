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

    protected $table = 'terms';
    protected $guarded = ['id'];
    protected $fillable = ['name', 'description', 'parent_id', 'uri', 'uri_type', 'exact_match_uri'];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    // public static function canView(){
    //     $a = '';
    // }

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

    protected $casts = [
        'uri_type' => UriTypes::class,
    ];

    protected static function booted()
    {
        static::creating(function (Term $term) {
            if ($term->uri_type === UriTypes::internal || empty($term->uri)) {
                $term->uri_type = UriTypes::internal;
                $term->uri = $term->generateInternalUri();
            }
        });

        static::updating(function (Term $term) {
            if ($term->isDirty('name') && $term->uri_type === UriTypes::internal) {
                $term->uri = $term->generateInternalUri();
            }
        });
    }

    public function generateInternalUri(): string
    {
        $baseUrl = rtrim(env('APP_URL', 'http://localhost'), '/');
        $termSlug = Str::slug($this->name);

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

        $appDomain = parse_url(env('APP_URL'), PHP_URL_HOST);
        $uriDomain = parse_url($this->uri, PHP_URL_HOST);

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

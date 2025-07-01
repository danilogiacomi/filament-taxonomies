<?php

namespace Net7\FilamentTaxonomies\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    protected $fillable = ['name', 'description', 'parent_id', 'uri', 'exact_match_uri'];

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

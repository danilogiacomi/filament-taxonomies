<?php

namespace Net7\FilamentTaxonomies\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConceptSchema extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'concept_schemas';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];

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
    public function concepts()
    {
        return $this->hasMany('\Net7\FilamentTaxonomies\Models\Concept');
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

    public function setLabelAttribute($value)
    {
        $value = strtolower($value);
        $this->attributes['label'] = $value;

    }

    public function setUriAttribute($value)
    {
        $value = strtolower($value);
        $explode_value = explode('/', $value);
        $this->attributes['uri'] = env("APP_URL")  . "/" . "taxonomy" . "/" . end($explode_value);
    }
}

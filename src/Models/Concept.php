<?php

namespace Net7\FilamentTaxonomies\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Concept extends Model
{
    use HasFactory;
    use \Net7\FilamentTaxonomies\Traits\TaxonomyTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'concepts';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $hidden = [];
    // protected $dates = [];
    protected $fillable = ['extras', 'label', 'uri', 'exact_match', 'parent_id', 'definition', 'conceptScheme'];
    protected $fakeColumns = ['extras'];
    protected $cast = [
        'extras' => 'array'
    ];


    private $rdf_type_uri = "https://www.w3.org/2009/08/skos-reference/skos.html#Concept";
    private $rdf_type_label = "Concept";

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
    public function parent()
    {
        return $this->belongsTo('Net7\FilamentTaxonomies\Models\Concept', 'parent_id');
    }

    public function conceptScheme()
    {
        return $this->belongsTo('Net7\FilamentTaxonomies\Models\ConceptSchema', 'concept_scheme_id');
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

    public function getRdfTypeUri(){
        return $this->rdf_type_uri;
    }

    public function getRdfTypeLabel(){
        return $this->rdf_type_label;
    }

    public function getSlug(){

        return Str::slug($this->label);
    }

    public function getRdfUri($baseUrl, $prefix){
        $uri = $baseUrl != "" ? $baseUrl . "/" : "";
        $uri .= $prefix != "" ? $prefix . "/#" . $this->getSlug() :  $this->getSlug();
        return $uri;
    }

    /**
     * Taxonomy Trait
     * Set the string to use for Tree label
     *
     */
    private function getLabelForTree(){
        return $this->label;
    }

    public function getRdfTypes(){
        $types = [];

        $types[] = [
            "uri" => $this->getRdfTypeUri(),
            "label" => $this->getRdfTypeLabel()
        ];
        return $types;
    }

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

    public function setExtrasAttribute($value)
    {

        $value = json_decode($value, true);
        $uri = $value['uri'];

        $explode_concept = explode('/', $uri);
        $concept_schema = ConceptSchema::where('id', $value['conceptScheme'])->value('label');

        $this->attributes['uri'] = env("APP_URL")  . "/" . "taxonomy" . "/" . $concept_schema . "#" . end($explode_concept);
        $this->attributes['concept_scheme_id'] = $value['conceptScheme'];

    }


}

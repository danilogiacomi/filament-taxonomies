<?php

namespace Net7\FilamentTaxonomies\Models;

use Illuminate\Database\Eloquent\Model;

class EntityTerm extends Model
{
    protected $fillable = [
        'entity_type',
        'entity_id', 
        'taxonomy_type',
        'term_id'
    ];

    public function entity()
    {
        return $this->morphTo();
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }
}
<?php

namespace net7\FilamentTaxonomies\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ConceptCollection extends ResourceCollection
{

    public static $wrap = "@graph";
    public function toArray($request)
    {
        return ConceptResource::collection($this->collection);
    }
}

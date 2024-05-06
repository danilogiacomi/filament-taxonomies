<?php

namespace net7\FilamentTaxonomies\Http\Resources;

use net7\FilamentTaxonomies\Models\Concept;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use ML\JsonLD\JsonLD;

class ConceptResource extends JsonResource
{

    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'user';

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $narrow = Concept::where('id', $this->parent_id)->first();

        $array = [
            "@id" => $this->uri,
          "@type" => "Concept",
          "preflabel" => $this->label,
          "definition" => $this->definition
        ];

        if (isset($narrow)) {
            $array['narrow'][] = [
                "@id" => $narrow->uri,
                "@type" => "Concept",
                "preflabel" => $narrow->label,
            ];
        }
        else {

            $broaders = Concept::where('parent_id', $this->id)->get();

            foreach ($broaders as $broader) {

                $topConcept[] = [
                '@id' => $broader->uri,
                '@type' => "Concept",
                    "preflabel" => $broader->label
                ];

            }
            if (isset($topConcept)) {
                $array["broader"] = $topConcept;
            }
        }

        return  $array;
    }



}

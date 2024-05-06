<?php

namespace Net7\FilamentTaxonomies\Http\Controllers;

use Net7\FilamentTaxonomies\Models\Concept;
use Net7\FilamentTaxonomies\Models\ConceptSchema;
use Net7\FilamentTaxonomies\Services\VocabularyJSONLD;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ML\JsonLD\JsonLD;
use ML\JsonLD\NQuads;

class Concepts extends Controller
{
    private mixed $schema_id;
    private string $schema;
    private Request $request;


    public function __invoke(Request $request, $schema)
    {
        $this->request = $request;
        $this->schema = $schema;

        if (!$this->existConceptSchema()) {
            return abort(404);
        }
        else
        {
            return $this->contentNegotiation();
        }
    }

    public function contentNegotiation()
    {
        if ($this->headerAccepted('text/html'))
        {
            return $this->displayHtml();
        }
        elseif ($this->headerAccepted('application/rdf+xml'))
        {
            return $this->displayRdf();
        }
        else
        {
            return $this->displayJsonld();
        }
    }


    public function displayHtml()
    {
        return $this->buildView();
    }

    public function displayRdf()
    {
        $graph = new  VocabularyJSONLD("jsonld/skos.jsonld", $this->schema_id);
        $graph = $graph->graph();
        $quads = JsonLD::toRdf($graph);
        $nquads = new NQuads();
        return $nquads->serialize($quads);
    }

    public function displayJsonld()
    {
        $graph = new  VocabularyJSONLD("jsonld/skos.jsonld", $this->schema_id);
        $graph = $graph->graph();
        return JsonLD::expand($graph, Storage::disk('public')->get("jsonld/skos.jsonld"));
    }


    public function headerAccepted($type): bool
    {
        $accept = $this->request->header('accept');
        $accept_exploded = explode( ';', $accept );
        $all_accepted = [];
        foreach ($accept_exploded as $accepted) {
            $all_accepted = array_merge(explode( ',', $accepted), $all_accepted);
        }

        return in_array($type, $all_accepted);
    }


    public function existConceptSchema(): bool
    {
        $this->schema_id = ConceptSchema::where('label', 'LIKE', '%'.$this->schema.'%')->value('id');

        return isset($this->schema_id);
    }

    public function buildView()
    {
        $schema = ConceptSchema::where('label', 'LIKE', '%'.$this->schema.'%')->get()->first()->toArray();
        $concepts = Concept::where('concept_scheme_id', $schema['id'])->get();
        $data = [];

        $schema['label'] = ucfirst($schema['label']);

        foreach ($concepts as $concept) {
            $broaders = Concept::where('id', $concept->parent_id)->get();
            $narrowers = Concept::where('parent_id', $concept->id)->get();
            $data[] = [
            'label' => $concept->label,
            'definition' => $concept->definition,
            'uri' =>  $concept->uri,
            'type' => "Concept",
            'exact_match' =>  $concept->exact_match,
            'inSchema' =>  $schema['label'],
            'inSchema_uri' =>  $schema['uri'],
            'broaders' => $broaders,
            'narrowers' => $narrowers
        ];
        }
        $onto = new VocabularyJSONLD("jsonld/skos.jsonld", $this->schema_id);
        $all_classes = $onto->getClasses();
        $all_annotations = $onto->getAnnotationsProperties();
        $classes = [];
        $annotations = [];

        foreach ($all_classes as $class) {

            $label_class = explode('#', $class);

            $classes[] = [
                "label" => $label_class[1],
                "uri"=> $class,
                "ontology" => $label_class[0]
            ];
        }
        foreach ($all_annotations as $annotation) {

            $label_class = explode('#', $annotation);

            $annotations[] = [
                "label" => $label_class[1],
                "uri"=> $annotation,
            ];
        }

        return view('vocabulary/index-en_test', ['individuals' => $data, "schema"=>$schema, "classes" => $classes, 'annotations' => $annotations]);
    }





}

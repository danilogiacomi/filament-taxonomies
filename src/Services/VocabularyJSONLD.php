<?php

namespace Net7\FilamentTaxonomies\Services;

use Net7\FilamentTaxonomies\Models\ConceptSchema;
use Net7\FilamentTaxonomies\Models\Concept;
use Illuminate\Support\Facades\Storage;
use ML\JsonLD\JsonLD;


class VocabularyJSONLD {

    # classes
    private static string $concept = "http://www.w3.org/2004/02/skos/core#Concept";
    private static string $conceptScheme = "http://www.w3.org/2004/02/skos/core#ConceptScheme";

    # annotation properties
    private static string $definition = "http://www.w3.org/2004/02/skos/core#definition";
    private static string $narrower = "http://www.w3.org/2004/02/skos/core#narrower";
    private static string $prefLabel = "http://www.w3.org/2004/02/skos/core#prefLabel";
    private static string $broader = "http://www.w3.org/2004/02/skos/core#broader";
    private static string $inSchema = "http://www.w3.org/2004/02/skos/core#inSchema";
    private static string $exactMatch = "http://www.w3.org/2004/02/skos/core#exactMatch";
    private static string $hasTopConcept = "http://www.w3.org/2004/02/skos/core#hasTopConcept";
    private static string $ontology = "http://www.w3.org/2002/07/owl#Ontology";
    private static string $versionIRI = "http://www.w3.org/2002/07/owl#versionIRI";
    private static string $title = "http://purl.org/dc/terms/title";
    private static string $description = "http://purl.org/dc/elements/1.1/description";
    private static string $creator = "http://purl.org/dc/terms/creator";
    private static string $license = "http://purl.org/dc/elements/1.1/rights";
    private static string $date = "http://purl.org/dc/elements/1.1/date";


    private mixed $graph;
    private string $context;
    private mixed $concept_schema_id;
    private array $classes;
    private array $annotationsProperties;


    public function __construct($context, $concept_schema_id) {

        $this->annotationsProperties = [
            "http://www.w3.org/2004/02/skos/core#definition",
            "http://www.w3.org/2004/02/skos/core#narrower",
            "http://www.w3.org/2004/02/skos/core#prefLabel",
            "http://www.w3.org/2004/02/skos/core#broader",
            "http://www.w3.org/2004/02/skos/core#inSchema",
            "http://www.w3.org/2004/02/skos/core#exactMatch",
            "http://www.w3.org/2004/02/skos/core#hasTopConcept",
            "http://www.w3.org/2002/07/owl#Ontology",
            "http://www.w3.org/2002/07/owl#versionIRI",
        ];

        $this->classes =  [
            "http://www.w3.org/2004/02/skos/core#Concept",
            "http://www.w3.org/2004/02/skos/core#ConceptScheme"
        ];

        $this->concept_schema_id = $concept_schema_id;


        $path = public_path('vendor/filament-taxonomies/'.$context);
        // $this->context = Storage::disk('local')->get('../vendor/filament-taxonomies/'.$context);

        $this->context = file_get_contents(public_path('vendor/filament-taxonomies/'.$context));
        
        $doc = JsonLD::getDocument($this->context);
        $this->graph = $doc->getGraph();

        $this->addMainClasses();
        //  $this->addConceptScheme();
        $this->addConcepts();

        if (Concept::count() > 0) {
            $this->addOntologyInfo();
        }

    }

    public function graph()
    {
        return $this->graph->toJsonLd();
    }

    public function getClasses(): array
    {
        return $this->classes;
    }

    public function getAnnotationsProperties(): array
    {
        return $this->annotationsProperties;
    }


    private function addMainClasses(): void
    {
        # add to class Concept
        $this->graph->createNode(self::$concept);
        # add to class ConceptScheme
        $this->graph->createNode(self::$conceptScheme);
    }

    private function addOntologyInfo(): void
    {
        $ontology_owl_class = $this->graph->createNode(self::$ontology);
        $concept_schema = ConceptSchema::where('id', $this->concept_schema_id)->first();
        $ontology_node = $this->graph->createNode($concept_schema->uri)->setType($ontology_owl_class);
        # IRI
        $all_update = Concept::all()->sortBy('updated_at')->pluck('created_at')->last()->format('Y-m-d');
        $ontology_node->addPropertyValue(self::$versionIRI, $concept_schema->uri . "/" . strval($all_update));
        # title
        $ontology_node->addPropertyValue(self::$title, $concept_schema->title);
        # description
        $ontology_node->addPropertyValue(self::$description, $concept_schema->description);
        # pref label
        $ontology_node->addPropertyValue(self::$prefLabel, $concept_schema->label);
        # creator
        $ontology_node->addPropertyValue(self::$creator, $concept_schema->creator);
        # license
        $ontology_node->addPropertyValue(self::$license, $concept_schema->license);
        # exact match
        $ontology_node->addPropertyValue(self::$exactMatch, $concept_schema->exact_match);
        # exact match
        $ontology_node->addPropertyValue(self::$date, strval($all_update));

    }

    private function addConceptScheme(): void
    {
        if ($this->concept_schema_id == 'all') {
            # retrieve all concept scheme
            $conceptScheme = ConceptSchema::all();
        }
        else
        {
            $conceptScheme = ConceptSchema::where('id', $this->concept_schema_id)->get();
        }

        foreach ($conceptScheme as $conceptSchema) {

            # take the class of ConceptScheme
            $conceptScheme_node = $this->graph->getNode(self::$conceptScheme);
            # add to class an individual
            $new_node = $this->graph->createNode($conceptSchema->uri)->setType($conceptScheme_node);
            # add to the individual a definition and the label
            $new_node->addPropertyValue(self::$definition, $conceptSchema->description);
            $new_node->addPropertyValue(self::$prefLabel, $conceptSchema->label);

            $topConcepts = Concept::where('parent_id', null)->where('concept_schema_id', $conceptSchema->id)->get();
            foreach ($topConcepts as $topConcept) {
                $new_node->addPropertyValue(self::$hasTopConcept, $topConcept->uri);
            }
        }
    }

    private function addConcepts(): void
    {
        if ($this->concept_schema_id == 'all') {
            # retrieve all concept scheme
            $concepts = Concept::all();
        }
        else
        {
            $concepts = Concept::where('concept_schema_id', $this->concept_schema_id)->get();
        }

        $this->addClassConcepts($concepts);
        $this->addPropertyConcepts($concepts);

    }

    private function addClassConcepts($concepts): void
    {
        foreach ($concepts as $concept) {
            $node = $this->graph->getNode(self::$concept);
            $conceptSchema = ConceptSchema::where('id', $concept->concept_schema_id)->value('uri');
            $conceptSchema_node = $this->graph->getNode($conceptSchema);
            $new_node = $this->graph->createNode($concept->uri)->setType($node);
            $new_node->addPropertyValue(self::$definition, $concept->definition);
            $new_node->addPropertyValue(self::$prefLabel, $concept->label);
            $new_node->addPropertyValue(self::$inSchema, $conceptSchema_node);
            $new_node->addPropertyValue(self::$exactMatch, $concept->exact_match);
        }

    }

    private function addPropertyConcepts($concepts): void
    {

        foreach ($concepts as $concept) {

            $main_node = $this->graph->getNode($concept->uri);

            $narrows = Concept::where('parent_id', $concept->id)->get();
            foreach ($narrows as $narrow) {
                $node = $this->graph->getNode($narrow->uri);
                $main_node->addPropertyValue(self::$narrower, $node);
            }

            $broaders = Concept::where('id', $concept->parent_id)->get();
            foreach ($broaders as $broader) {
                $node = $this->graph->getNode($broader->uri);
                $main_node->addPropertyValue(self::$broader, $node);
            }
        }
    }

}

<?php

namespace net7\FilamentTaxonomies\Http\Controllers\Api;

use net7\FilamentTaxonomies\Models\Concept;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ConceptController extends Controller
{

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function index(Request $request)
    {

        $search_term = $request->input('q'); // the search term in the select2 input
        // if you are inside a repeatable we will send some aditional data to help you
        $triggeredBy = $request->input('triggeredBy'); // you will have the `fieldName` and the `rowNumber` of the element that triggered the ajax

        // NOTE: this is a Backpack helper that parses your form input into an usable array.
        // you still have the original request as `request('form')`
        $form = backpack_form_input();

        $options = Concept::query();

        // if no category has been selected, show no options
        if (! $form['conceptScheme']) {
            return [];
        }

        // if a category has been selected, only show articles in that category
        if ($form['conceptScheme']) {
            $options = $options->where('concept_scheme_id', $form['conceptScheme']);
        }

        if ($search_term) {
            $results = $options->where('label', 'LIKE', '%'.$search_term.'%')->paginate(10);
        } else {
            $results = $options->paginate(10);
        }

        return $results;
    }

    public function show($id)
    {
        return Concept::find($id);
    }
}

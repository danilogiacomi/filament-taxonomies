<?php

namespace Net7\FilamentTaxonomies\Http\Controllers;

use Net7\FilamentTaxonomies\Models\Document;
use Net7\FilamentTaxonomies\Services\TeipublisherService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TeipubController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function redirectEditXml(Request $request){
        $teipub_services = new TeipublisherService();
         $data = (Array) $teipub_services->getInstance()->loginUser(env('TEIPUB_COOKIE_DOMAIN'));
        
        if(isset($data['error'])){
            return back()->withErrors($data['error']);
        } else {
          $document = Document::find($request->id);
          $annotate_link = $document->transcriptions->sortDesc()->first()->annotateXmlFile();
          return redirect($annotate_link);
        }
    }
}

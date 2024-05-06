<?php
namespace Net7\FilamentTaxonomies\Services;

use Net7\FilamentTaxonomies\Services\MurucaCoreV2TeiPublisher;

class TeipublisherService {

    private $teiPublisher;

    public function __construct(){
        $teipuburl = config('muruca.teipublisher.url');
        $collection = config('muruca.teipublisher.collection');        
        $token = config('muruca.teipublisher.token');        
        $this->teiPublisher = new MurucaCoreV2TeiPublisher($teipuburl, $collection, $token);
    }

    public function sendFile( $file, $path, $file_name ){ 
        return $this->teiPublisher->teiPublisherUploadToCollection($path);               
    }

    public function getXmlContent($filepath){
        $xml = $this->teiPublisher->teiPublisherGetXml($filepath);
        return $xml;    
    }

    public function getHtmlContent($filepath){
        $html = $this->teiPublisher->teiPublisherGetHtml($filepath);
        return $html;    
    }
    
    public function getInstance(){
        return $this->teiPublisher;
    }

    /*public function createTranscription($xml, $html, $record_id){
        $transcription = new Transcription(['title' => 'prova', 'xml' => $xml, 'html' => $html, 'record_id' => $record_id]);
        $transcription->save();
        return $transcription->id;
    }*/
    
}
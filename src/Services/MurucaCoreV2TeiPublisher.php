<?php

namespace net7\FilamentTaxonomies\Services;

use ErrorException;
use Psy\Readline\Hoa\Console;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://netseven.it
 * @since      1.0.0
 *
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 */
class MurucaCoreV2TeiPublisher {

    private $tei_publisher_url;
    private $tei_publisher_profile;
    private $token;

    private static $upload_api_path = "exist/apps/tei-publisher/api/upload/";
    private static $register_data = "exist/apps/tei-publisher/api/mrcregister/";
    private static $login_api = "exist/apps/tei-publisher/api/login/";
    //private $get_html_api_path = "/exist/apps/tei-publisher/api/document/"; api for html page
    private $get_html_api_path = "exist/apps/tei-publisher/api/parts/";
    private static $get_xml_api_path = "exist/apps/tei-publisher/api/document/";
    private static $get_html_conversion_path = "exist/apps/tei-publisher/";

    public function __construct( $url, $profile, $token = "") {
        $this->tei_publisher_url = $url . "/";
        $this->tei_publisher_profile = $profile;
        $this->token = $token;
    }

    public static function getTeiPublisherCollectionUrl($baseurl, $filename, $collection = ""){
        $url = $baseurl . "/" . self::$get_xml_api_path ;
        $document = $collection != "" ? $collection . $filename : $filename;
        return $url . $document;
    }

    public static function getTeiPublisherHtmlUrl($baseurl, $filename, $collection = "", $odd = "", $template =""){
        $url = $baseurl . "/" . self::$get_html_conversion_path ;
        $document = $collection != "" ? $collection . $filename : $filename;
        $odd = "?odd=$odd". ".odd";
        $temp = $template != "" ? "&template=$template" : "";
        return $url . $document . $odd . $temp;
    }

    /* Uploads file on teipublisher */
    public function teiPublisherUploadToCollection( $filepath ) {

        $curl = curl_init();
        $curlFile = new \CURLFile($filepath);
        $options = array(
            CURLOPT_HEADER          => true,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_URL             => $this->tei_publisher_url . self::$upload_api_path. $this->tei_publisher_profile,
            CURLOPT_POST 	    => true,
            CURLOPT_POSTFIELDS      => array("files[]" => $curlFile),
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => array(
                "Accept: application/json",
                "Content-Type: multipart/form-data",
                "Authorization: Basic " . $this->token
            ),
        );
        curl_setopt_array($curl, $options);

        $response = curl_exec($curl);
        if(curl_error($curl)) {
            return ["error" => curl_error($curl)];
        } else {
            $info = curl_getinfo($curl);
            $header = substr($response, 0, $info['header_size']);
            $respBody = json_decode(substr($response, strlen($header)));
            curl_close($curl);
            if( $info && $info["http_code"] == 200 ){
                return $respBody[0]->path;
            }
            else {
                return ["error" => "error ". $respBody->description];
            }
        }
        return null;
    }

    /* Calls Teipublisher API to het html version of a file */
    public function teiPublisherGetHtml($path){
        $odd = config('muruca.teipublisher.odd');
        $url = $this->tei_publisher_url
        . $this->get_html_api_path
        . urlencode ($this->tei_publisher_profile . $path )
        ."/json?odd=". $odd .".odd&view=single";

        $curl2 = curl_init();
        curl_setopt_array($curl2, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => array(
                "Accept: application/json",
                "Authorization: Basic " . $this->token
            ),
        ));
        $response = curl_exec($curl2);
        if(curl_error($curl2)) {
            return ["error" => curl_error($curl2)];
        } else {
            $info = curl_getinfo($curl2);
            $respBody = json_decode($response);
            curl_close($curl2);
            if( !empty($respBody->content) ){
                return $respBody->content;
            } else {
                return ["error" => "error ". $info["http_code"]];
            }
        }
    }

    public function teiPublisherGetXmlUrl($path) {
        return $this->tei_publisher_url
            . self::$get_xml_api_path
            . $this->tei_publisher_profile . "/"
            . $path;
    }

    /* Calls Teipublisher API to het html version of a file */
    public function teiPublisherGetXml($path){
        $odd = config('muruca.teipublisher.odd');
        $url = $this->tei_publisher_url
        . self::$get_xml_api_path
        . urlencode ($this->tei_publisher_profile . $path );


        $curl2 = curl_init();
        curl_setopt_array($curl2, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => array(
                "Accept: application/json",
                "Authorization: Basic " . $this->token
            ),
        ));
        $response = curl_exec($curl2);
        if(curl_error($curl2)) {
            return ["error" => curl_error($curl2)];
        } else {
            curl_close($curl2);
            if($response && !empty($response) ){
                return $response;
            }
            else {
                return ["error" => "error  empty response"];
            }
        }
    }

    public function teiPublisherUploadFile( $filepath ) {
        $curl = curl_init();
        $curlFile = new CURLFile($filepath);
        $options = array(
            CURLOPT_HEADER          => true,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_URL             => $this->tei_publisher_url  . self::$upload_api_path . $this->tei_publisher_profile,
            CURLOPT_POST 	    => true,
            CURLOPT_POSTFIELDS      => array("files[]" => $curlFile),
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => array(
                "Accept: application/json",
                "Content-Type: multipart/form-data",
                "Authorization: Basic " . $this->token
            ),
        );
        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);
        if(curl_error($curl)) {
            return ["error" => curl_error($curl)];
        } else if ($response) {
            $info = curl_getinfo($curl);
            $header = substr($response, 0, $info['header_size']);
            $respBody = json_decode(substr($response, strlen($header)));
            $path = $this->tei_publisher_profile . $respBody[0]->path;
            return $path;
        }
        return null;
    }


    /**
     * sendEntityToRegister
     *
     * @param  string $name
     * @param  string $id
     * @param  string $type possible values "place"|"person"
     * @param  mixed $data other data to send, example
     *  {
     *    note: "",
     *    lat: "",
     *    lng: "",
     *    links: []
     *  }
     * @return object
     */
    public function sendEntityToRegister($name, $id, $type, $data = []){
        if(!$name || !$id || !$type){
            throw new ErrorException("required data (name, id or type) missing");
        }
        $url = self::$register_data . "$type/$id";
        $data['name'] = $name;
        if( $type == "place" && !isset($data["links"]) ){
            $data["links"] = [""];
        }
        $response = $this->sendData($url, $data);
        return $response;
    }

    public function getEntityFromRegister($id, $type){
        $url = self::$register_data . "$type/$id";
        $response = $this->getData($url);
        if(isset($response->id)){
            return $response;
        } else return  false;

    }

    /**
     * loginUser
     *
     * @return Object
     */

    public function loginUser($cookieDomain = ""){
        $login_data =  explode(":", base64_decode($this->token));
        if(count($login_data) == 2){
            $url = self::$login_api ."?user=" . $login_data[0] . "&password=" . $login_data[1] ;
            $response = $this->sendData($url, [], "", true);
            if($response['header']){
                $cookies = CurlService::curl_get_cookie($response['header']);
                if($cookies['JSESSIONID']){
                    setcookie('JSESSIONID',$cookies['JSESSIONID'],0,'/exist', $cookieDomain);
                }
                return $response['body'];
            }
        } else {
            return ["error" => "incorrect teiPublisher username or password"];
        }

    }

    private function sendData($url, $data = [], $filepath = "", $setHeader = false){
        $curl = curl_init();
        if($filepath) {
            $curlFile = new CURLFile($filepath);
            $data["files[]"] = $curlFile;
        }

        $options = $this->getCurlOptions($url,
            [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HEADER => $setHeader
            ]
        );
        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);
        return $this->parseCurlResponse($curl, $response, $setHeader);
    }

    private function getData($url){
        $curl = curl_init();
        $options = $this->getCurlOptions($url);
        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);
        return $this->parseCurlResponse($curl, $response);
    }

    private function getCurlOptions($url, $options = []){
        $default_options = array(
            CURLOPT_HEADER          => false,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_POST      => false,
            CURLOPT_URL             => $this->tei_publisher_url  . $url,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => array(
                "Accept: application/json",
                "Content-Type: application/json",
                "Authorization: Basic " . $this->token
            )
        );

        foreach($options as $key => $val){
            $default_options[$key] = $val;
        }
        return $default_options;
    }

    private function parseCurlResponse($curl, $curl_response, $return_header = false){
        $info = curl_getinfo($curl);
        if( $info && $info["http_code"] != 200 ){

            $respBody = CurlService::curl_get_body($curl, $curl_response);
            return ["error" => "error ". $respBody->description];
        }
        if(curl_error($curl)) {
            return ["error" => curl_error($curl)];
            curl_close($curl);
        } else if ($curl_response) {
            if( $return_header ){
                return [
                 "header" => CurlService::curl_get_header($curl, $curl_response),
                 "body" => CurlService::curl_get_body($curl, $curl_response)
                ];
            } else
               return CurlService::curl_get_body($curl, $curl_response);
        }

    }


}

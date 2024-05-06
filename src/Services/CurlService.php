<?php

namespace net7\FilamentTaxonomies\Services;

use net7\FilamentTaxonomies\Services\MurucaCoreV2TeiPublisher;

class CurlService
{

    public static function curl_get_body($ch, $response)
    {
        $body = json_decode($response);
        if ($body == null) {
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $body = json_decode(substr($response, $header_size));
        }
        return $body;
    }

    public static function curl_get_header($ch, $response)
    {
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        return $header;
    }

    public static function curl_get_cookie($response)
    {
        preg_match_all('/Set-Cookie:\s*([^;]*)/mi', $response, $matches);
        $cookies = array();
        foreach ($matches[1] as $item) {
            parse_str($item, $cookie);
            $cookies = array_merge($cookies, $cookie);
        }
        return $cookies;
    }
}

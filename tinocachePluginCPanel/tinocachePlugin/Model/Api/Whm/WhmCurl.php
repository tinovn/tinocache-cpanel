<?php

namespace tinocachePlugin\Model\Api\Whm;

use tinocachePlugin\Model\Api\AbstractCurl;
use tinocachePlugin\Model\Api\AbstractApi;

class WhmCurl extends AbstractCurl
{
    public function exec( $postData, AbstractApi $api)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $api->getUrl());
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $header[0] = "Authorization: WHM root:" .preg_replace("'(\r|\n)'","",$api->detailsProvider->getAuthkey());
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
       
        if( isset($postData) && !empty($postData) ){
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postData) );
        }
        $e = curl_exec($curl);
        return $e; 
    }
}
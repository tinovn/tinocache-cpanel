<?php

namespace tinocachePlugin\Model\Api;

abstract class AbstractCurl
{
    /**
     * Performs the request to api
     * @param array $postData
     * @param AbstractApi $api
     */
    abstract function exec( $postData, AbstractApi $api );
}
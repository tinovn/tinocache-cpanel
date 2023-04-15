<?php

namespace tinocachePlugin\Model\Api\Whm\Functions;

use tinocachePlugin\Model\Api\ApiMethodInterface;
use tinocachePlugin\Model\Api\Whm\WhmApi;

class DumpZone extends WhmApi implements ApiMethodInterface
{

    public $method = 'GET';
    public $domain;
    private $path  = '/dumpzone';

    public function getUrl()
    {
        return $this->detailsProvider->getEndpoint().'/json-api/'.$this->path.'?api.version=2&domain='.$this->domain;
    }

    public function handleResponse($response)
    {
        return $response->data->zone[0]->record;
    }
}

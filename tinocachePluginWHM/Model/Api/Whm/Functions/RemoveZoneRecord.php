<?php

namespace tinocachePlugin\Model\Api\Whm\Functions;

use tinocachePlugin\Model\Api\ApiMethodInterface;
use tinocachePlugin\Model\Api\Whm\WhmApi;

class RemoveZoneRecord extends WhmApi implements ApiMethodInterface
{

    public $method = 'GET';
    public $zone;
    public $line;
    private $path  = '/removezonerecord';

    public function getUrl()
    {
        $url = '/json-api/'.$this->path;
        $url .= '?zone='.$this->zone;
        $url .= '&line='.$this->line;

        return $this->detailsProvider->getEndpoint().$url;
    }

    public function handleResponse($response)
    {
        return $response;
    }
}

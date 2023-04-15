<?php

namespace tinocachePlugin\Model\Api\Whm\Functions;

use tinocachePlugin\Model\Api\ApiMethodInterface;
use tinocachePlugin\Model\Api\Whm\WhmApi;

class AddZoneRecord extends WhmApi implements ApiMethodInterface
{

    public $method = 'GET';
    public $domain;
    public $name;
    public $nsdname;
    public $address;
    public $type   = 'NS';
    public $class  = 'IN';
    public $ttl    = '86400';
    private $path  = '/addzonerecord';

    public function getUrl()
    {
        $url = '/json-api/'.$this->path;
        $url .= '?domain='.$this->domain;
        $url .= '&name='.$this->name;
        $url .= '&nsdname='.$this->nsdname;
        $url .= '&type='.$this->type;
        $url .= '&class='.$this->class;
        $url .= '&ttl='.$this->ttl;
        $url .= '&address='.$this->address;

        return $this->detailsProvider->getEndpoint().$url;
    }

    public function handleResponse($response)
    {
        return $response;
    }
}

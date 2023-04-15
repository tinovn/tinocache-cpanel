<?php

namespace tinocachePlugin\Model\Api\Whm\Functions;

use tinocachePlugin\Model\Api\ApiMethodInterface;
use tinocachePlugin\Model\Api\Whm\WhmApi;

class EditZoneRecord extends WhmApi implements ApiMethodInterface
{

    public $method = 'GET';
    public $domain;
    public $line;
    public $name;
    public $class;
    public $ttl;
    public $type;
    public $address;
    private $path  = '/editzonerecord';

    public function getUrl()
    {
        $url = '/json-api/'.$this->path.'?api.version=2';
        $url .= '&domain='.$this->domain;
        $url .= '&line='.$this->line;
        $url .= '&name='.$this->name;
        $url .= '&class='.$this->class;
        $url .= '&ttl='.$this->ttl;
        $url .= '&type='.$this->type;
        $url .= '&address='.$this->address;

        return $this->detailsProvider->getEndpoint().$url;
    }

    public function handleResponse($response)
    {
        return $response;
    }
}

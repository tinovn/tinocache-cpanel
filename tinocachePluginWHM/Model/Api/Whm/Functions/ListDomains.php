<?php
namespace tinocachePlugin\Model\Api\Whm\Functions;

use tinocachePlugin\Model\Api\ApiMethodInterface;
use tinocachePlugin\Model\Api\Whm\WhmApi;

class ListDomains extends WhmApi implements ApiMethodInterface
{

    public $method = 'GET';
    private $path = '/listaccts';

    public function getUrl()
    {
        return $this->detailsProvider->getEndpoint() . '/json-api/' . $this->path . '?api.version=2';
    }

    public function handleResponse($response)
    {
        return $response->data->acct;
    }
}

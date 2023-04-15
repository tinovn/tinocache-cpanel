<?php
namespace tinocachePlugin\Model\Api\Whm\Functions;

use tinocachePlugin\Model\Api\ApiMethodInterface;
use tinocachePlugin\Model\Api\Whm\WhmApi;

class Test extends WhmApi implements ApiMethodInterface
{

    public $method = 'GET';
    private $path = '/listaccts';

    public function getUrl()
    {
        return $this->detailsProvider->getEndpoint() . '/execute/DomainInfo/domains_data';
    }

    public function handleResponse($response)
    {
        return $response->data->acct;
    }
}

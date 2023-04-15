<?php

namespace tinocachePlugin\Model\Api\Whm\Functions;

use tinocachePlugin\Model\Api\ApiMethodInterface;
use tinocachePlugin\Model\Api\Whm\WhmApi;

class ListAddonDomains extends WhmApi implements ApiMethodInterface
{

    public $method = 'GET';
    public $regex;
    public $user;
    private $result;

    public function getUrl()
    {
        $params = [
            'cpanel_jsonapi_user'       => 'bartek22',
            'cpanel_jsonapi_apiversion' => 3,
            'cpanel_jsonapi_module'     => 'DomainInfo',
            'cpanel_jsonapi_func'       => 'domains_data'
        ];

        if (!empty($this->regex))
        {
            $params['regex'] = $this->regex;
        }

        return $this->detailsProvider->getEndpoint().'/json-api/cpanel?'.http_build_query($params);
    }

    public function handleResponse($response)
    {
        return $response;
    }

    public function getAddonDomainsNumber()
    {
        return count($this->result);
    }
}

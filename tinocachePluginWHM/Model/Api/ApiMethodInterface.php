<?php

namespace tinocachePlugin\Model\Api;

interface ApiMethodInterface
{

    /**
     * Get the full Url for reqeust
     */
    function getUrl();

    /**
     * Get from response usefull data
     * @param object $response
     */
    function handleResponse($response);
}

<?php

namespace tinocachePlugin\Model\Api\Whm;

use tinocachePlugin\Model\Api\AbstractApi;
use tinocachePlugin\Model\Api\Whm\Factory\ValidatorFactory;

abstract class WhmApi extends AbstractApi
{
    public function __construct()
    {
        parent::__construct( 
            new WhmDetailsProvider(), 
            ValidatorFactory::createResponseValidator(), 
            new WhmCurl()
        );
    }
}
<?php

namespace tinocachePlugin\Model\Api\Whm\Factory;

use tinocachePlugin\Model\Api\ResponseValidator;
use tinocachePlugin\Model\Api\Whm\Validator\ResultValidator;
use tinocachePlugin\Model\Api\Whm\Validator\EndpointValidator;
use tinocachePlugin\Model\Api\Whm\Validator\AuthenticationValidator;

class ValidatorFactory
{
    public static function createResponseValidator()
    {
        $validators = [];

        $validators[] = new EndpointValidator();
        $validators[] = new AuthenticationValidator();
        $validators[] = new ResultValidator();

        return new ResponseValidator( $validators );
    }
}
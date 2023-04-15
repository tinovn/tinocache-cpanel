<?php
namespace tinocachePlugin\Model\Api\Whm\Validator;

use tinocachePlugin\Model\Api\AbstractValidator;

class EndpointValidator extends AbstractValidator
{

    public function validate($response)
    {
        $this->createErrorMessage();

        return (bool) $response;
    }

    private function createErrorMessage()
    {
        $this->errorMessage = "Whm api: Specified endpoint or url is invalid.";
    }
}

<?php
namespace tinocachePlugin\Model\Api\Whm\Validator;

use tinocachePlugin\Model\Api\AbstractValidator;

class AuthenticationValidator extends AbstractValidator
{

    public function validate($response)
    {
        if (isset($response->cpanelresult) && isset($response->cpanelresult->error)) {
            $this->createErrorMessage($response);

            return false;
        }

        return true;
    }

    private function createErrorMessage($response)
    {
        $this->errorMessage = "Whm api: " . $response->cpanelresult->error;
    }
}

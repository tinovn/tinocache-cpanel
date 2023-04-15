<?php

namespace tinocachePlugin\Model\Api\Whm\Validator;

use tinocachePlugin\Model\Api\AbstractValidator;

class ResultValidator extends AbstractValidator
{

    public function validate($response)
    {
        if (!isset($response->metadata)) {
            return true;
        }

        if (!$response->metadata->result && !$response->cpanelresult)
        {
            $this->createErrorMessage($response);

            return false;
        }

        return true;
    }

    public function createErrorMessage($response)
    {
        $this->errorMessage = "WHM API:".$response->metadata->reason;
    }
}

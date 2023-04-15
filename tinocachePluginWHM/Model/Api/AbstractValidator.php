<?php 

namespace tinocachePlugin\Model\Api;

abstract class AbstractValidator
{
    /**
     *
     * @var string
     */
    protected $errorMessage;
    
    /**
     * Validates Api response
     */
    abstract function validate( $response );
    
    /**
     * 
     * @return String
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }
}
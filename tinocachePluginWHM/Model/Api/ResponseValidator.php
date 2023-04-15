<?php

namespace tinocachePlugin\Model\Api;

use tinocachePlugin\Model\Tool\InstantFailMessage;

class ResponseValidator
{
    /**
     * @var array tinocachePlugin\Model\Api\AbstractValidator
     */
    public $validators;
    
    /**
     * 
     * @param array tinocachePlugin\Model\Api\AbstractValidator
     */
    public function __construct( array $validators )
    {
        $this->validators = $validators;
    }
    
    /**
     * Validate Api response
     * @param object $response
     */
    public function validate( $response, $errors)
    {
        foreach( $this->validators as $validator ){
            $result = $validator->validate( $response );
            
            if( !$result && $errors === false){
                InstantFailMessage::create( $validator->getErrorMessage() );
            }
        }
    }
}
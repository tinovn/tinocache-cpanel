<?php

namespace tinocachePlugin\Model\HTTP;

class Request
{
    private $request;
    
    public function __construct( array $request )
    {
        $this->request = $request;
    }
    
    
    public function get( $key )
    {
        if( !$this->exists($key) ){
            return null;
        }
        
        return $this->request[ $key ];
    }
    
    
    public function exists( $key )
    {
        return (bool) array_key_exists( $key, $this->request );
    }
    
    
    public function all()
    {
        return $this->request;
    }
    
    public function isEmpty()
    {
        return (bool) empty( $this->request ); 
    }
}
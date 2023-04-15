<?php
namespace tinocachePlugin\Model\Hook;

class HookInvoker
{
    private $hook;
    private $params;
    
    public function __construct( $input )
    {
        $hookName = HookRequestReader::readHookAction( $input );
        
        $this->hook = AvailableHooks::get( $hookName );
        $this->params = HookRequestReader::read();
    }
    
    public function execute()
    {
        return $this->hook->execute( $this->params );
    }
}
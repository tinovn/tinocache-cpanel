<?php
namespace tinocachePlugin\Model\Hook;

class AvailableHooks
{
    const ACTION_NAMESPACE = 'tinocachePlugin\\Model\\Hook\\Action\\';
    
    const HOOK_CLASSES = [
        'create',
        'suspend',
        'unsuspend',
        'terminate',
        'changePackage'
    ];
    
    public static function get( $className )
    {
        if( self::classAvailable( $className ) ){
            $className = self::ACTION_NAMESPACE . ucfirst( $className );
            
            return new $className();
        }
    }

    public static function classAvailable( $className )
    {
        return (bool) in_array( $className, self::HOOK_CLASSES );
    }
}
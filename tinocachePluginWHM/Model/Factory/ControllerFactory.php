<?php 

namespace tinocachePlugin\Model\Factory;

class ControllerFactory
{
    const PATH = 'tinocachePlugin\\Controller\\';
    
    
    public static function create( $controllerName, \tinocachePlugin\View\View $view )
    {
        $controller =  self::PATH . $controllerName . 'Controller';
        $controller = new $controller( $view );
        
        if( ! $controller instanceof \tinocachePlugin\Controller\AbstractController){
            throw new \Exception('Given controller is not instance of AbstractController');
        }
        
        return $controller;
    }    
}
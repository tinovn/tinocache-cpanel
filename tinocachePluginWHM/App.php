<?php

namespace tinocachePlugin;

use tinocachePlugin\Model\Db\Database;
use tinocachePlugin\Model\Db\SchemaCreator;
use tinocachePlugin\View\View;
use tinocachePlugin\Model\Factory\ControllerFactory;
use tinocachePlugin\Model\HTTP\Request;

class App
{

    /**
     * @var string
     */
    private $controllerName;

    /**
     * @var string
     */
    private $actionName;

    /**
     *
     * @var string
     */
    private $templateName;

    /**
     *
     * @var View
     */
    private $view;

    /**
     * @var Request
     */
    private $request;

    /**
     * App constructor
     * @throws \Exception
     */
    public function __construct($controllerName, $actionName, $request)
    {
        Database::setup();
        SchemaCreator::create();

        $this->view           = new View;
        $this->controllerName = $controllerName;
        $this->actionName     = $actionName;
        $this->templateName   = $this->actionName;
        $this->request        = new Request($request);
    }

    /**
     * create controller, call the method and display the view
     */
    public function init()
    {
        $controller = ControllerFactory::create($this->controllerName, $this->view);

        call_user_func([$controller, $this->actionName], $this->request);

        $this->view->render($this->controllerName.DS.$this->templateName);
    }
}

<?php

namespace tinocachePlugin\Controller;

use tinocachePlugin\View\View;
use tinocachePlugin\Model\HTTP\Request;

abstract class AbstractController
{
    /**
     *
     * @var View $view
     */
    public $view;

    /**
     * constructor for childs class
     * @param View $view
     */
    public function __construct(View $view)
    {
        $this->view = $view;
    }

    /**
     * default controller action
     * @param Request $request
     */
    abstract function index( Request $request );

}
<?php

namespace tinocachePlugin\Controller;

class Controller
{
    public function view($name)
    {
        return require_once '/usr/local/cpanel/base/frontend/jupiter/tinocachePlugin/View/' . $name . '.php';
    }
}

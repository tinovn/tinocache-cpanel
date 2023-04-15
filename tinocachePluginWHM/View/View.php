<?php

namespace tinocachePlugin\View;

use tinocachePlugin\Model\Tool\Path;

class View
{

    /**
     * @var array
     */
    public $menuItems;

    /**
     * @param string $name
     * @param string $value
     */
    public function __set($name, $value)
    {
        $this->{$name} = $value;
    }

    /**
     * loads menu items from xml file
     */
    public function loadMenuItems()
    {
        $this->menuItems = simplexml_load_file(Path::build('config.xml'));
    }

    /**
     * display view
     * @param string$viewName
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function render($viewName)
    {
        $this->loadMenuItems();

        $viewPath = Path::build('View', $viewName.'.php');

        require_once( Path::build('View', 'Layout.php') );
    }

    public function exists($viewName)
    {
        return file_exists(Path::build('View', $viewName.'.php'));
    }
}

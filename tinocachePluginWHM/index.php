<?php

require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/autoload.php';
require_once __DIR__.'/constants.php';

if (isset($argv[1]) && $argv[1] == 'install')
{
    if (PHP_SAPI != 'cli')
    {
        die;
    }

    // Create tables
    tinocachePlugin\Model\Db\Database::setup();
    tinocachePlugin\Model\Db\SchemaCreator::create();
    
    $config = \tinocachePlugin\Model\Db\Table\Configuration::instance();
    $config->ServerAddr  = 'localhost';
    $config->ServerPort     = '22';
    $config->ServerUsername = 'root';
    $config->save();
    die;
}

if (isset($_GET['controller']))
{
    $controller = $_GET['controller'];
}
else
{
    $controller = 'Index';
}

if (isset($_GET['action']))
{
    $action = $_GET['action'];
}
else
{
    $action = 'index';
}

unset($_POST['controller']);
unset($_POST['action']);

$app = new tinocachePlugin\App($controller, $action, $_POST);
$app->init();

#!/usr/local/cpanel/3rdparty/bin/php -q
<?php
namespace tinocachePlugin\Model\Hook;

use tinocachePlugin\Model\Db\Database;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../constants.php';

register_shutdown_function(['tinocachePlugin\Model\Tool\Logger', 'debug']);

error_reporting(E_ALL);
ini_set("display_errors", 1);

$input = (count($argv) > 1) ? $argv : array();

//used for hook registration
if (in_array('--describe', $input)) {
    echo trim(json_encode(describe()));
    exit;
}

//other hooks actions
Database::setup();

if ($input) {
    $hookInvoker = new HookInvoker($input);
    $result = $hookInvoker->execute();

    echo trim(json_encode($result));
}

function describe()
{
    return [
        ['category' => 'Whostmgr',
            'event' => 'Accounts::Create',
            'stage' => 'post',
            'hook' => '/usr/local/cpanel/whostmgr/docroot/cgi/tinocachePlugin/Model/Hook/hooks.php --create',
            'exectype' => 'script'
        ],
        ['category' => 'Whostmgr',
            'event' => 'Accounts::Remove',
            'stage' => 'post',
            'hook' => '/usr/local/cpanel/whostmgr/docroot/cgi/tinocachePlugin/Model/Hook/hooks.php --terminate',
            'exectype' => 'script'
        ],
        ['category' => 'Whostmgr',
            'event' => 'Accounts::change_package',
            'stage' => 'post',
            'hook' => '/usr/local/cpanel/whostmgr/docroot/cgi/tinocachePlugin/Model/Hook/hooks.php --changePackage',
            'exectype' => 'script'
        ],
        ['category' => 'Whostmgr',
            'event' => 'Accounts::suspendacct',
            'stage' => 'post',
            'hook' => '/usr/local/cpanel/whostmgr/docroot/cgi/tinocachePlugin/Model/Hook/hooks.php --suspend',
            'exectype' => 'script'
        ],
        ['category' => 'Whostmgr',
            'event' => 'Accounts::unsuspendacct',
            'stage' => 'post',
            'hook' => '/usr/local/cpanel/whostmgr/docroot/cgi/tinocachePlugin/Model/Hook/hooks.php --unsuspend',
            'exectype' => 'script'
        ],
    ];
}

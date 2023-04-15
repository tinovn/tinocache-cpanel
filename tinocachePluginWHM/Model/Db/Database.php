<?php

namespace tinocachePlugin\Model\Db;

use Illuminate\Database\Capsule\Manager as Capsule;
use tinocachePlugin\Model\Tool\Path;

class Database
{

    public static function setup()
    {
        $capsule = new Capsule;
        $capsule->addConnection([
            'driver'    => 'sqlite',
            'database'  => __DIR__.'/tinocachePlugin.sqlite',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]);

        // Make this Capsule instance available globally via static methods... (optional)
        $capsule->setAsGlobal();

        // Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
        $capsule->bootEloquent();

        @chmod(dirname(__FILE__) . "/tinocachePlugin.sqlite", 0777);
    }
}

<?php

namespace tinocachePlugin\Model\Db;

use Illuminate\Database\Capsule\Manager as Capsule;

class SchemaCreator
{

    static $path = 'tinocachePlugin\Model\Db\\Schema\\';

    public static function create()
    {
        $tables = self::getTables();

        foreach ($tables as $table)
        {
            if (!Capsule::schema()->hasTable(strtolower($table)))
            {
                $class = self::$path.$table;
                $class::create();
            }
        }
    }

    private static function getTables()
    {
        return ['Configuration', 'Customer', 'Logs'];
    }
}

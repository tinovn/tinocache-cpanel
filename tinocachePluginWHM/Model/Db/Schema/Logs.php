<?php

namespace tinocachePlugin\Model\Db\Schema;

use Illuminate\Database\Capsule\Manager as Capsule;

class Logs
{

    /**
     * Create table
     */
    public static function create()
    {
        if (!Capsule::schema()->hasTable('tinocache_logs'))
        {
            Capsule::schema()->create('tinocache_logs', function($table) {
                $table->increments('id');
                $table->string('action')->nullable();
                $table->string('request')->nullable();
                $table->string('response')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public static function drop()
    {
        Capsule::schema()->dropIfExists('tinocache_logs');
    }

    public static function truncate()
    {
        Capsule::table('tinocache_logs')->truncate();
    }
}

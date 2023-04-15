<?php

namespace tinocachePlugin\Model\Db\Schema;

use Illuminate\Database\Capsule\Manager as Capsule;

class Configuration
{

    /**
     * Create table
     */
    public static function create()
    {
        if (!Capsule::schema()->hasTable('tinocache_configuration'))
        {
            Capsule::schema()->create('tinocache_configuration', function($table) {
                $table->increments('id');
                $table->string('ServerAddr');
                $table->string('ServerPort');
                $table->string('ServerUsername');
                $table->string('ServerPassword')->nullable();
                $table->string('SaslConfPath')->nullable();
                $table->string('MemcacheBin')->nullable();
                $table->string('MemcacheCacheSize')->nullable();
                $table->string('MemcacheUser')->nullable();
                $table->integer('PortFrom')->nullable();
                $table->integer('PortTo')->nullable();
                $table->string('isUsed')->default(1);
                $table->string('disableSASL')->default(1);
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
        Capsule::schema()->dropIfExists('tinocache_configuration');
    }

    public static function truncate()
    {
        Capsule::table('tinocache_configuration')->truncate();
    }
}

<?php

namespace tinocachePlugin\Model\Db\Schema;

use Illuminate\Database\Capsule\Manager as Capsule;

class Customer
{

    /**
     * Create table
     */
    public static function create()
    {
        if (!Capsule::schema()->hasTable('tinocache_customer'))
        {
            Capsule::schema()->create('tinocache_customer', function($table) {
                $table->increments('id');
                $table->string('cpanelusername');
                $table->string('memusername')->nullable();
                $table->string('mempassword')->nullable();
                $table->integer('port');
                $table->boolean('active')->default(false);
                $table->string('disabledSASL')->default('0');
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
        Capsule::schema()->dropIfExists('tinocache_customer');
    }

    public static function truncate()
    {
        Capsule::table('tinocache_customer')->truncate();
    }
}

<?php

namespace tinocachePlugin\Model\Db\Table;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{

    protected $table    = 'tinocache_customer';
    //protected $fillable = ['cpaneluser', 'cwatchuser', 'isLogged', 'camid', 'password'];

    public static function get()
    {
        if (self::isEmpty())
        {
            return new self();
        }

        return self::first();
    }

    public static function isEmpty()
    {
        return !(bool)self::count();
    }
}

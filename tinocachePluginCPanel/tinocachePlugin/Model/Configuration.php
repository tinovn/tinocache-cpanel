<?php

namespace tinocachePlugin\Model;

use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{

    protected $table    = 'tinocache_configuration';
    //protected $fillable = ['action', 'request', 'response'];

    public static function instance()
    {
            return new self();
    }

    public static function getActiveConfig()
    {
        return self::where('isUsed', '1')->first();
    }
}

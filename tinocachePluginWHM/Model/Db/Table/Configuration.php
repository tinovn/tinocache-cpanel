<?php

namespace tinocachePlugin\Model\Db\Table;

use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{

    protected $table    = 'tinocache_configuration';
    protected $fillable = ['ServerAddr', 'ServerPort', 'ServerUsername', 'SaslConfPath',
        'MemcacheBin', 'MemcacheCacheSize', 'MemcacheUser', 'PortFrom', 'PortTo', 'disableSASL'];

    public static function instance()
    {
        return new self();
    }
}

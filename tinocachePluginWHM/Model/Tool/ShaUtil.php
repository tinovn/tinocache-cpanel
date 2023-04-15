<?php

namespace tinocachePlugin\Model\Tool;

class ShaUtil
{

    public static function sha1hex($string)
    {
        return (sha1($string));
    }
}

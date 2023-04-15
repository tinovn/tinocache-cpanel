<?php

namespace tinocachePlugin\Model\Tool;

use tinocachePlugin\View\View;

class InstantFailMessage
{

    public static function create($message)
    {
        Logger::debug($message);

        var_dump($message);
    }
}

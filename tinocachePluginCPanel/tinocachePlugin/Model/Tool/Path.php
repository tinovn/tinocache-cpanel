<?php

namespace tinocachePlugin\Model\Tool;

class Path
{

    public static function build(...$segments)
    {
        $path = MODULE_DIR;
        foreach ($segments as $segment) {
            $path .= DS . $segment;
        }

        return $path;
    }

}

<?php

namespace tinocachePlugin\Model\Tool;

class JsonTool
{

    public static function isJson($string)
    {
        $a = json_decode($string);

        if (ob_get_contents())
        {
            ob_clean();
        }

        if (json_last_error() === JSON_ERROR_NONE)
        {
            return true;
        }

        return false;
    }
}

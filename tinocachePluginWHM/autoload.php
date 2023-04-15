<?php

/**
 * Created by ModulesGarden.
 *
 * PHP version 7
 *
 * @author Bartosz Brożek <bartosz.br@modulesgarden.com>
 * @link https://www.modulesgarden.com/
 *
 *  * ******************************************************************
 *
 * This software is furnished under a license and may be used and copied
 * only  in  accordance  with  the  terms  of such  license and with the
 * inclusion of the above copyright notice.  This software  or any other
 * copies thereof may not be provided or otherwise made available to any
 * other person.  No title to and  ownership of the  software is  hereby
 * transferred.
 *
 *  * ******************************************************************
 */
include('/usr/local/cpanel/base/frontend/jupiter/tinocachePlugin/vendor/autoload.php');
spl_autoload_register(
        function ($className)
{
    if (strpos($className, "tinocachePlugin") === 0)
    {
        $className = str_replace("\\", DIRECTORY_SEPARATOR, $className);
        $file      = str_replace("tinocachePlugin", dirname(__FILE__), $className) . '.php';

        if (file_exists($file))
        {
            require_once $file;
        }
        else
        {
            $className = str_replace("\\", DIRECTORY_SEPARATOR, $className);
            $file      = str_replace("tinocachePlugin", '/usr/local/cpanel/base/frontend/jupiter/tinocachePlugin', $className) . '.php';
            if (file_exists($file))
            {
                require_once $file;
            }
        }
    }
}
);

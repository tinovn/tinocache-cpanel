<?php
namespace tinocachePlugin\Model\Tool;

use tinocachePlugin\Model\Db\Table\Logs;

class Logger
{

    public static function debug($data = '', $action = '', $request = '', $response = '')
    {
        try {
            if (!empty($data)) {
                $file = Path::build('log.txt');

                $time = PHP_EOL . '[' . date("Y-m-d H:m") . "]";
                if (empty($data)) {
                    $data = error_get_last();
                }

                $errorContent = var_export($data, true);

                if ($errorContent == null) {
                    return;
                }
                file_put_contents($file, $time, FILE_APPEND);
                file_put_contents($file, $errorContent, FILE_APPEND);
            }

            if (!empty($action)) {
                $logs = new Logs;
                $logs->action = $action;
                $logs->request = $request;
                $logs->response = $response;
                $logs->save();
            }
        } catch (\PDOException $ex) {
            
        }
    }
}

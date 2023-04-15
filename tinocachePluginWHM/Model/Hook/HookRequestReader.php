<?php
namespace tinocachePlugin\Model\Hook;

class HookRequestReader
{

    public static function read()
    {
        // Get input from STDIN.
        $raw_data = '';
        $stdin_fh = fopen('php://stdin', 'r');
        if (is_resource($stdin_fh)) {
            stream_set_blocking($stdin_fh, 0);
            while (($line = fgets($stdin_fh, 1024)) !== false) {
                $raw_data .= trim($line);
            }
            fclose($stdin_fh);
        }

        // Process and JSON-decode the raw output.
        if ($raw_data) {
            $input_data = json_decode($raw_data, true);
        } else {
            $input_data = ['context' => [], 'data' => [], 'hook' => []];
        }

        // Return the output.
        return $input_data['data'];
    }

    public static function readHookAction($input)
    {
        return substr($input[1], 2);
    }
}

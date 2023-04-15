<?php

namespace tinocachePlugin\Model\Api\CPanel;

class Cli
{

    protected $dumpSensitiveData = [];

    protected function execute($cmd, $workdir = null)
    {

        $this->resetLastExec();

        if (is_null($workdir))
        {
            $workdir = __DIR__;
        }

        $descriptorspec = [
            0 => ["pipe", "r"], // stdin
            1 => ["pipe", "w"], // stdout
            2 => ["pipe", "w"], // stderr
        ];

        // Run each command as sudo
        $cmd = "sudo ".$cmd;

        $process = proc_open($cmd, $descriptorspec, $pipes, $workdir, null);

        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        $returnCode = proc_close($process);

        $this->lastExec = [
            'command'    => $cmd,
            'stdout'     => $stdout,
            'stderr'     => $stderr,
            'returnCode' => $returnCode,
        ];

        if ($returnCode != 0)
        {
            return [
                'error' => [
                    'cmd'        => $cmd,
                    'info'       => trim(strtok($stderr, "\n")),
                    'returnCode' => $returnCode
                ]
            ];
        }

        return trim($stdout);
    }

    private function resetLastExec()
    {
        $this->lastExec = [];
    }

    private function hideDumpSensitiveData($dump)
    {
        return str_replace($this->dumpSensitiveData, '**HIDDEN**', $dump);
    }

    protected function dumpLastExec($saveToFile = false)
    {

        if (empty($this->lastExec))
        {
            return "No command executed";
        }

        $dump = "=== DATE ===\n";
        $dump .= date("Y-m-d H:i:s");
        $dump .= "\n=== COMMAND ===\n";
        $dump .= trim(preg_replace('/\s+/', ' ', $this->lastExec['command']));
        $dump .= "\n=== STDOUT ===\n";
        $dump .= trim(preg_replace('/\s+/', ' ', $this->lastExec['stdout']));
        $dump .= "\n=== STDERR ===\n";
        $dump .= trim(preg_replace('/\s+/', ' ', $this->lastExec['stderr']));
        $dump .= "\n=== RETURN CODE ===\n";
        $dump .= $this->lastExec['returnCode'];
        $dump .= "\n======\n";

        if ($saveToFile)
        {
            file_put_contents(__DIR__.'/../../logs/cli.log', $dump.PHP_EOL, FILE_APPEND | LOCK_EX);
        }

        return $this->hideDumpSensitiveData($dump);
    }

    protected function executeAsSu($cmd, $username, $pass)
    {
        return $this->execute("su - $username <<!
            $pass $cmd");
    }

    private function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}

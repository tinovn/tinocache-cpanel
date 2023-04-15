<?php

namespace tinocachePlugin\Service;

use Illuminate\Database\Capsule\Manager as DB;
use \tinocachePlugin\Model\Configuration;
use \phpseclib\Net\SSH2 as SSH2;
use \tinocachePlugin\Model\Logs;
use \tinocachePlugin\Model\Db\Table\Customer as User;

class AccountHelper
{
    public $ssh;
    public $host;
    public $username;
    public $instancePort;
    public $licensekey;

    public function __construct($username, $host=null)
    {
        if (session_status() == PHP_SESSION_NONE)
        {
            session_start();
        }

        if($username)
        {
            $this->username = $username;
        }
        if(!is_null($host)) {
            $this->host = $host;
        }
        else
        {
            $this->host = Configuration::getActiveConfig();
        }

    }

    public function connect($host, $port, $username, $password)
    {
        $ssh = new SSH2($host, $port, 1);
        if (!$ssh->login($username, $password))
        {
            return ['error' => 'Error: Login Failed'];
        }
        return $ssh;
    }

    public function connectToSSH()
    {
        $this->ssh =
                $this->connect(
                        $this->host->ServerAddr,
                        $this->host->ServerPort,
                        $this->host->ServerUsername,
                        $this->host->ServerPassword
                    );

        Logs::create(['action'   => __FUNCTION__,
            'request'  => $this->host->ServerAddr . "\n" .
            $this->host->ServerPort . "\n" .
            $this->host->ServerUsername . "\n" .
            $this->host->ServerPassword,
            'response' => is_array($this->ssh) ? $this->ssh['error'] : 'Success'
        ]);

        if(!is_array($this->ssh))
            return $this->ssh;
        else
            return false;
    }
    public function findServiceByPID()
    {
        $response = $this->ssh->exec('ps -p $(cat ' . $this->host->SaslConfPath . '/' . $this->username . '/pid) | grep $(cat ' . $this->host->SaslConfPath . '/' . $this->username . '/pid)');

        Logs::create(['action'   => __FUNCTION__,
            'request'  => 'ps -p $(cat ' . $this->host->SaslConfPath . '/' . $this->username . '/pid) | grep $(cat ' . $this->host->SaslConfPath . '/' . $this->username . '/pid)',
            'response' => $response
        ]);
        if (strpos($response, 'error') !== false)
        {
            return '';
        }
        return $response;
    }

    public function generateRandomPassword()
    {
        return substr(md5(mt_rand()), 0, 7);
    }
    public function exportVar()
    {
       $this->ssh->write('export SASL_CONF_PATH='.$this->host->SaslConfPath .'/'.$this->username."\n");
       $this->ssh->read();
       $this->ssh->write("printenv\n");

       Logs::create(['action'   => __FUNCTION__,
            'request'  => 'export SASL_CONF_PATH='.$this->host->SaslConfPath .'/'.$this->username."\n",
            'response' => $this->ssh->read()
        ]);

       return $this->ssh->read();

    }
    public function getAvailablePort()
    {
        $portsFromDB = DB::table('tinocache_customer')->select('port')->get()->keyBy('port')->toArray();

        for ($i = $this->host->PortFrom; $i <= $this->host->PortTo; $i++)
        {
            if (!array_key_exists($i, $portsFromDB))
            {
                $this->instancePort = $i;
                break;
            }
        }
        return $this->instancePort;
    }

    public function changeOwner()
    {
        $this->ssh->write('chown '.$this->host->MemcacheUser.": ".$this->host->SaslConfPath . '/' . $this->username . "/sasldb\n");
        $read = $this->ssh->read();

        Logs::create(['action'   => __FUNCTION__,
            'request'  => 'chown '.$this->host->MemcacheUser.": ".$this->host->SaslConfPath . '/' . $this->username . '/sasldb',
            'response' => $read
        ]);
    }
    public function setSaslDBFile()
    {
        $this->ssh->write('saslpasswd2 -a tinocache -c -f ' . $this->host->SaslConfPath . '/' . $this->username . '/sasldb ' . $this->username . "\n");
        $read = $this->ssh->read();

        Logs::create(['action'   => __FUNCTION__,
            'request'  => 'saslpasswd2 -a tinocache -c -f ' . $this->host->SaslConfPath . '/' . $this->username . '/sasldb ' . $this->username . "\n",
            'response' => $read
        ]);

        $random           = $this->generateRandomPassword();
        $this->ssh->write($random . "\n");
        $read = $this->ssh->read();


        Logs::create(['action'   => __FUNCTION__,
            'request'  => 'Pass:'.$random,
            'response' => $read
        ]);

        $this->ssh->write($random . "\n");
        $this->randomPass = $random;

        Logs::create(['action'   => __FUNCTION__,
            'request'  => 'Pass Again:'.$random,
            'response' => $this->ssh->read()
        ]);

        return $random;
    }

    public function createTinocacheConfig()
    {
        $configContent = "mech_list: plain\nlog_level: 5\nsasldb_path: " . $this->host->SaslConfPath . '/' . $this->username.'/sasldb';
        $this->ssh->write('echo -e "' . $configContent . '" > ' . $this->host->SaslConfPath . '/' . $this->username."/tinocache.conf\n");
        $read = $this->ssh->read();

        Logs::create(['action'   => __FUNCTION__,
            'request'  => 'echo -e "' . $configContent . '" > ' . $this->host->SaslConfPath . '/' . $this->username."/tinocache.conf\n",
            'response' => $read
        ]);

        return $this->ssh->read();
    }

    public function runTinocache($disablesasl = false)
    {
        $port = $this->getAvailablePort();

        $this->ssh->write($this->host->MemcacheBin . ' -d -m 64 -p ' . $port . " -u ".$this->host->MemcacheUser." -l ".$this->host->ServerAddr."".(!$disablesasl ? ' -S' : '')."\n");
        $read = $this->ssh->read();

        Logs::create(['action'   => __FUNCTION__,
            'request'  => $this->host->MemcacheBin . ' -d -m 64 -p ' . $port . " -u ".$this->host->MemcacheUser." -l ".$this->host->ServerAddr."".(!$disablesasl ? ' -S' : '')."\n",
            'response' => $read
        ]);

        return $port;
    }
    public function savePidToFile()
    {
        $this->ssh->write("pgrep -xn tinocache > ".$this->host->SaslConfPath . "/" . $this->username."/pid\n");
        //$this->ssh->write('echo $! > '.$this->host->SaslConfPath . "/" . $this->username."/pid\n");
        $read = $this->ssh->read();
        Logs::create(['action'   => __FUNCTION__,
            'request'  => "pgrep -xn tinocache > ".$this->host->SaslConfPath . "/" . $this->username."/pid\n",
            'response' => $read
        ]);

        return $read;
    }

    public function removeTinocacheDB()
    {
        $read = $this->ssh->exec('rm -r '.$this->host->tinocacheConfPath . "/" . $this->username);
        Logs::create(['action'   => __FUNCTION__,
            'request'  => 'rm -r '.$this->host->tinocacheConfPath . "/" . $this->username,
            'response' => $read
        ]);
    }

    public function killByName($name = 'tinocache')
    {
        $response = $this->ssh->exec('pkill -f "'.$name.'"');
        Logs::create(['action'   => __FUNCTION__,
            'request'  => 'pkill -f "'.$name.'"',
            'response' => $response
        ]);
    }

    public function createUserDir()
    {
        $this->ssh->read();
        $this->ssh->write('mkdir ' . $this->host->tinocacheConfPath . '/' . $this->username."\n");
        $read = $this->ssh->read();

        Logs::create(['action'   => __FUNCTION__,
            'request'  => 'mkdir ' . $this->host->tinocacheConfPath . '/' . $this->username,
            'response' => $read
        ]);
    }

    public function StatusByType($type)
    {

      if ($type == 'memcached') {
        $cmd = "pgrep -xn memcached";
        $response = exec($cmd);
        if ($response) {
           return 'Enable';
        }
      }

      if ($type == 'redis') {
        $cmd = "pgrep -xn redis-server";
        $response = exec($cmd);
        if ($response) {
           return 'Enable';
        }
      }


      return 'Disable';
    }
    public function MemoryByType($type)
    {
      // $response = $this->ssh->exec();
      if ($type == 'memcached') {
        return '64MB';
      }
      return '64MB';
    }

    public function ActiveByType($type){

      if ($type == 'memcached') {
        $cmd = 'pkill -f memcached';
        $response = exec($cmd);
        $cmd =  '/usr/bin/memcached -d -B ascii -m 64 -s /home/'.$this->username.'/.tino/memcached.sock';
        $response = exec($cmd);
        Logs::create(['action'   => __FUNCTION__,
            'request'  => $cmd,
            'response' => $response
        ]);
        $filename = '/home/'.$this->username.'/.tino/.etc/memcached_enable';
        if (!file_exists($filename)) {
          $handle = fopen($filename, 'w') or die('Cannot open file:  '.$filename);
        }
      }
      if ($type == 'redis') {
        //$kill = exec('pkill redis-server');
        $response = exec('/usr/bin/cre_redis');

        Logs::create(['action'   => __FUNCTION__,
            'request'  => '/usr/bin/cre_redis',
            'response' => $response
        ]);

        $filename = '/home/'.$this->username.'/.tino/.etc/redis_enable';
        if (!file_exists($filename)) {
          $handle = fopen($filename, 'w') or die('Cannot open file:  '.$filename);
        }


      }

    }
    public function RebuidByType($type)
    {
      $filename = '/home/'.$this->username.'/.tino/.etc';
      if (!file_exists($filename)) {
        mkdir($filename, 0755, true);
      }
      $this->ActiveByType($type);
    }

    public function killByPID($type)
    {
      if ($type == 'memcached') {
        $cmd = 'pkill -f memcached';
        $response = exec($cmd);
        Logs::create(['action'   => __FUNCTION__,
            'request'  => $cmd,
            'response' => $response
        ]);
        $filename = '/home/'.$this->username.'/.tino/memcached.sock';
        unlink($filename);
        unlink('/home/'.$this->username.'/.tino/.etc/memcached_enable');
      }
      if ($type == 'redis') {
        $cmd = 'pkill redis-server';
        $response = exec($cmd);
        Logs::create(['action'   => __FUNCTION__,
            'request'  => $cmd,
            'response' => $response
        ]);
        $filename = '/home/'.$this->username.'/.tino/redis.sock';
        unlink($filename);
        unlink('/home/'.$this->username.'/.tino/.etc/redis_enable');
      }
    }


    public function testServer($username, $password, $port)
    {

    }


    public function check_license($localkey='') {

        require dirname(__FILE__) . DIRECTORY_SEPARATOR . "../license.php";
        $licensekey = $TinoCP_unix_socket_licensekey;
        // Enter the url to your WHMCS installation here
        $whmcsurl = 'https://my.tino.org/';
        // Must match what is specified in the MD5 Hash Verification field
        // of the licensing product that will be used with this check.
        $licensing_secret_key = 'f213cb63f0e14e84e809a42d2206559e';
        // The number of days to wait between performing remote license checks
        $localkeydays = 15;
        // The number of days to allow failover for after local key expiry
        $allowcheckfaildays = 5;

        // -----------------------------------
        //  -- Do not edit below this line --
        // -----------------------------------

        $check_token = time() . md5(mt_rand(100000000, mt_getrandmax()) . $licensekey);
        $checkdate = date("Ymd");
        $domain = $_SERVER['SERVER_NAME'];
        $usersip = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : $_SERVER['LOCAL_ADDR'];
        $dirpath = dirname(__FILE__);
        $verifyfilepath = 'modules/servers/licensing/verify.php';
        $localkeyvalid = false;
        if ($localkey) {
            $localkey = str_replace("\n", '', $localkey); # Remove the line breaks
            $localdata = substr($localkey, 0, strlen($localkey) - 32); # Extract License Data
            $md5hash = substr($localkey, strlen($localkey) - 32); # Extract MD5 Hash
            if ($md5hash == md5($localdata . $licensing_secret_key)) {
                $localdata = strrev($localdata); # Reverse the string
                $md5hash = substr($localdata, 0, 32); # Extract MD5 Hash
                $localdata = substr($localdata, 32); # Extract License Data
                $localdata = base64_decode($localdata);
                $localkeyresults = json_decode($localdata, true);
                $originalcheckdate = $localkeyresults['checkdate'];
                if ($md5hash == md5($originalcheckdate . $licensing_secret_key)) {
                    $localexpiry = date("Ymd", mktime(0, 0, 0, date("m"), date("d") - $localkeydays, date("Y")));
                    if ($originalcheckdate > $localexpiry) {
                        $localkeyvalid = true;
                        $results = $localkeyresults;
                        $validdomains = explode(',', $results['validdomain']);
                        if (!in_array($_SERVER['SERVER_NAME'], $validdomains)) {
                            $localkeyvalid = false;
                            $localkeyresults['status'] = "Invalid";
                            $results = array();
                        }
                        $validips = explode(',', $results['validip']);
                        if (!in_array($usersip, $validips)) {
                            $localkeyvalid = false;
                            $localkeyresults['status'] = "Invalid";
                            $results = array();
                        }
                        $validdirs = explode(',', $results['validdirectory']);
                        if (!in_array($dirpath, $validdirs)) {
                            $localkeyvalid = false;
                            $localkeyresults['status'] = "Invalid";
                            $results = array();
                        }
                    }
                }
            }
        }
        if (!$localkeyvalid) {
            $responseCode = 0;
            $postfields = array(
                'licensekey' => $licensekey,
                'domain' => $domain,
                'ip' => $usersip,
                'dir' => $dirpath,
            );
            if ($check_token) $postfields['check_token'] = $check_token;
            $query_string = '';
            foreach ($postfields AS $k=>$v) {
                $query_string .= $k.'='.urlencode($v).'&';
            }

            if (function_exists('curl_exec')) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $whmcsurl . $verifyfilepath);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $data = curl_exec($ch);
                $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
            } else {
                $responseCodePattern = '/^HTTP\/\d+\.\d+\s+(\d+)/';
                $fp = @fsockopen($whmcsurl, 80, $errno, $errstr, 5);
                if ($fp) {
                    $newlinefeed = "\r\n";
                    $header = "POST ".$whmcsurl . $verifyfilepath . " HTTP/1.0" . $newlinefeed;
                    $header .= "Host: ".$whmcsurl . $newlinefeed;
                    $header .= "Content-type: application/x-www-form-urlencoded" . $newlinefeed;
                    $header .= "Content-length: ".@strlen($query_string) . $newlinefeed;
                    $header .= "Connection: close" . $newlinefeed . $newlinefeed;
                    $header .= $query_string;
                    $data = $line = '';
                    @stream_set_timeout($fp, 20);
                    @fputs($fp, $header);
                    $status = @socket_get_status($fp);
                    while (!@feof($fp)&&$status) {
                        $line = @fgets($fp, 1024);
                        $patternMatches = array();
                        if (!$responseCode
                            && preg_match($responseCodePattern, trim($line), $patternMatches)
                        ) {
                            $responseCode = (empty($patternMatches[1])) ? 0 : $patternMatches[1];
                        }
                        $data .= $line;
                        $status = @socket_get_status($fp);
                    }
                    @fclose ($fp);
                }
            }
            if ($responseCode != 200) {
                $localexpiry = date("Ymd", mktime(0, 0, 0, date("m"), date("d") - ($localkeydays + $allowcheckfaildays), date("Y")));
                if ($originalcheckdate > $localexpiry) {
                    $results = $localkeyresults;
                } else {
                    $results = array();
                    $results['status'] = "Invalid";
                    $results['description'] = "Remote Check Failed";
                    return $results;
                }
            } else {
                preg_match_all('/<(.*?)>([^<]+)<\/\\1>/i', $data, $matches);
                $results = array();
                foreach ($matches[1] AS $k=>$v) {
                    $results[$v] = $matches[2][$k];
                }
            }
            if (!is_array($results)) {
                die("Invalid License Server Response");
            }
            if ($results['md5hash']) {
                if ($results['md5hash'] != md5($licensing_secret_key . $check_token)) {
                    $results['status'] = "Invalid";
                    $results['description'] = "MD5 Checksum Verification Failed";
                    return $results;
                }
            }
            if ($results['status'] == "Active") {
                $results['checkdate'] = $checkdate;
                $data_encoded = json_encode($results);
                $data_encoded = base64_encode($data_encoded);
                $data_encoded = md5($checkdate . $licensing_secret_key) . $data_encoded;
                $data_encoded = strrev($data_encoded);
                $data_encoded = $data_encoded . md5($data_encoded . $licensing_secret_key);
                $data_encoded = wordwrap($data_encoded, 80, "\n", true);
                $results['localkey'] = $data_encoded;
            }
            $results['remotecheck'] = true;
        }
        unset($postfields,$data,$matches,$whmcsurl,$licensing_secret_key,$checkdate,$usersip,$localkeydays,$allowcheckfaildays,$md5hash);

        // $licenseresults = $license->check_license($this->licensekey);
        // Interpret response
        switch ($results['status']) {
            case "Active":
                // get new local key and save it somewhere
                $localkeydata = $results['localkey'];
                break;
            case "Invalid":
                die("License key is Invalid");
                break;
            case "Expired":
                die("License key is Expired");
                break;
            case "Suspended":
                die("License key is Suspended");
                break;
            default:
                die("Invalid Response");
                break;
        }

        return $results;
    }



}

<?php

namespace tinocachePlugin\Controller;

use Illuminate\Database\Capsule\Manager as DB;
use \tinocachePlugin\Service\AccountHelper;
use \tinocachePlugin\Model\Configuration;

class TinoCacheController extends Controller
{
    public $username;
    public $assets;
    public $status;
    public $result;
    public $service;
    public $licensekey;
    public $ssh;
    public $host;
    public $error;
    public $userPort;
    public $memcachedStatus;
    public $rdisStatus;
    public $memcachedMemory;
    public $rdisMemory;


    public function __construct($cpanel)
    {
        if (session_status() == PHP_SESSION_NONE){
            session_start();
        }

        \tinocachePlugin\Model\Db\Database::setup();

        $this->username  = $cpanel->cpanelprint('$user');

        $helper = new AccountHelper($this->username);
       
        //Get status and Memory cache
        $this->memcachedStatus = $helper->StatusByType('memcached');
        $this->redisStatus = $helper->StatusByType('redis');
        $this->memcachedMemory = $helper->MemoryByType('memcached');
        $this->redisMemory = $helper->MemoryByType('redis');

        $this->ac = filter_input(INPUT_GET, 'ac');

        if (filter_input(INPUT_POST, 'action') && (filter_input(INPUT_POST, 'action') == 'activate' || filter_input(INPUT_POST, 'action') == 'rebuild' || filter_input(INPUT_POST, 'action') == 'deactivate') || filter_input(INPUT_POST, 'action') == 'check')
        {
            $this->result = (object) $this->execute($_POST);
        }


        $this->assets = '/usr/local/cpanel/base/frontend/jupiter/tinocachePlugin/View/Assets/';
    }

    public function execute($post)
    {
      $helper     = new AccountHelper($this->username);
      $type =$post['type'];

        switch ($post['action'])
        {
            case 'activate':

            $filename = '/home/'.$this->username.'/.tngcache/.etc';

            if (!file_exists($filename)) {
              mkdir($filename, 0755, true);
            }
              if ($type == "memcached") {
                // code active memcache
                $helper->ActiveByType('memcached');
                header('Location: index.live.php?ac=memcached');
              } else {
                // code active redis
                $helper->ActiveByType('redis');
                header('Location: index.live.php?ac=redis');
              }

              exit;
            case 'rebuild':
                //Gọi Hàm rebuild

                if ($type == "memcached") {
                  $helper->RebuidByType('memcached');
                  header('Location: index.live.php?ac=memcached');
                } else {
                  // code rebuild redis
                  $helper->RebuidByType('redis');
                  header('Location: index.live.php?ac=redis');
                }
                exit;

                break;
            case 'deactivate':
                if ($type == "memcached") {
                  // code deactivate memcache
                  $helper->killByPID('memcached');
                  header('Location: index.live.php?ac=dememcached');
                } else {
                  // code deactivate redis
                  $helper->killByPID('redis');
                  header('Location: index.live.php?ac=deredis');
                }
                break;
            case 'check':
                break;
            default:
                break;
        }
    }
}

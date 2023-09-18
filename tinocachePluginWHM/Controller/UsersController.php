<?php

namespace tinocachePlugin\Controller;

use tinocachePlugin\Model\Api\Whm\Functions\ListDomains;
use tinocachePlugin\Model\HTTP\Request;
use Illuminate\Database\Capsule\Manager as DB;
use tinocachePlugin\View\View;
use Exception;
// use \phpseclib\Net\SSH2 as SSH2;
use tinocachePlugin\Model\Db\Table\Logs;
/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class UsersController extends AbstractController
{

    public function __construct(View $view)
    {
        parent::__construct($view);

        $action = filter_input(INPUT_GET, 'action');

        if (!empty($action))
        {
            $this->execute($action);
        }
    }

    /**
     *
     * @param Request $request
     */
    public function index(Request $request)
    {
        try
        {
            $domains = (new ListDomains())->request();



            foreach ($domains as $k=>&$domain)
            {

                $domain->MemcachedStatus = 1;
                $domain->RedisStatus = 1;

                $memcached = '/home/'.$domain->user.'/.tngcache/memcached.sock';
                if (!file_exists($memcached)) {
                  $domain->MemcachedStatus = 0;
                }

                $redis = '/home/'.$domain->user.'/.tngcache/redis.sock';
                if (!file_exists($redis)) {
                  $domain->RedisStatus = 0;
                }


            }

            $this->view->users = $domains;
        }
        catch (Exception $ex)
        {
            $this->view->errors = [$ex->getMessage()];
        }
    }

    private function getUserFromDB($username)
    {
        return DB::table('tinocache_customer')->where('cpanelusername', $username)->first();
    }

    public function execute($action)
    {
        if (ob_get_contents())
        {
            ob_clean();
        }

        header("Content-Type: application/json; charset=UTF-8");

        switch ($action)
        {
            case 'Deactive';
              $user = filter_input(INPUT_GET, 'username');
              $cmd = 'pkill -f memcached  -u '.$user.' && pkill redis-server  -u '.$user;
              $reponse = exec($cmd);
              $filename = '/home/'.$user.'/.tngcache/memcached.sock';
              unlink($filename);
              $filename = '/home/'.$user.'/.tngcache/redis.sock';
              unlink($filename);
              echo json_encode(['response' => 'ok']);

              $filename = '/home/'.$user.'/.tngcache/.etc/redis_enable';
              unlink($filename);
              $filename = '/home/'.$user.'/.tngcache/.etc/memcached_enable';
              unlink($filename);


              break;

            case 'Rebuild';

              $user = filter_input(INPUT_GET, 'username');
              $type = filter_input(INPUT_GET, 'type');

              $filename = '/home/'.$user.'/.tngcache/.etc';
              if (!file_exists($filename)) {
                mkdir($filename, 0755, true);
              }


              if ($type == 'memcached') {
                $filename = '/home/'.$user.'/.tngcache/memcached.sock';
                unlink($filename);
                $kill = exec('pkill -f memcached -u '.$user);
                $memcached = exec('/sbin/cagefs_enter_user '.$user.' /usr/bin/memcached -d -B ascii -m 64 -s /home/'.$user.'/.tngcache/memcached.sock');

                $filename = '/home/'.$user.'/.tngcache/.etc/memcached_enable';
                if (!file_exists($filename)) {
                  $handle = fopen($filename, 'w') or die('Cannot open file:  '.$filename);
                }

              }
              if ($type == 'redis') {
                $filename = '/home/'.$user.'/.tngcache/redis.sock';
                unlink($filename);
                //$kill = exec('pkill redis-server  -u '.$user);
                $redis = exec( '/sbin/cagefs_enter_user '.$user.' /usr/bin/cre_redis' );

                $filename = '/home/'.$user.'/.tngcache/.etc/redis_enable';
                if (!file_exists($filename)) {
                  $handle = fopen($filename, 'w') or die('Cannot open file:  '.$filename);
                }

              }

              echo json_encode(['response' => 'ok']);

              break;

            default:
                echo json_encode([]);
                break;
        }

        die();
    }
}

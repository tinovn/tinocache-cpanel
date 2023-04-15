<?php
namespace tinocachePlugin\Controller;

use tinocachePlugin\Model\Db\Table\Logs;
use tinocachePlugin\Model\HTTP\Request;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class LogsController extends AbstractController
{

    /**
     *
     * @param Request $request
     */
    public function index(Request $request)
    {
        if (isset($_GET['id'])) {
            $this->view->log = Logs::where('id', $_GET['id'])->first();
        }
        if (isset($_GET['clearLogs']) && $_GET['clearLogs'] == 'clearLogs') {
            $this->clearLogs();
        }

        $this->view->logs = Logs::all();
    }

    public function clearLogs()
    {
        if (ob_get_contents()) {
            ob_clean();
        }
        try {
            Logs::truncate();
            echo "SUCCESS";
        } catch (\PDOException $ex) {
            echo "FAILURE";
        }

        die();
    }
}

<?php

namespace tinocachePlugin\Controller;
use Exception;
use Illuminate\Database\Capsule\Manager as DB;
use tinocachePlugin\Model\Db\Table\Configuration;
use tinocachePlugin\Model\Db\Table\Customer;
use tinocachePlugin\Model\Db\Table\Logs;
use tinocachePlugin\Model\HTTP\Request;
use tinocachePlugin\View\View;
use \tinocachePlugin\Service\AccountHelper;

class IndexController extends AbstractController {
	public $username;
	public $assets;
	public $status;
	public $result;
	public $service;
	public $ssh;
	public $host;
	public $userPort;

	public function __construct(View $view) {
		parent::__construct($view);

		$action = filter_input(INPUT_GET, 'action');

		if (!empty($action)) {
			$this->execute($action);
		}
	}

	public function execute($action) {
		if (ob_get_contents()) {
			ob_clean();
		}

		header("Content-Type: application/json; charset=UTF-8");

		switch ($action) {
		case 'getConfig':
			$config = (new Configuration())->where('id', $_GET['config'])->first();
			unset($config->ServerPassword);
			echo json_encode($config);
			break;
		case 'delConfig':
			$config = (new Configuration())->where('id', $_GET['config'])->delete();
			Logs::create(['action' => 'delConfig', 'request' => 'id:' . $_GET['config'], 'response' => $config]);
			echo json_encode($config);
			break;
		case 'testConnection':
			$response = $this->testConnection();
			Logs::create(['action' => 'Test Connection', 'request' => "", 'response' => $response]);
			echo json_encode($response);
			break;
		case 'SetAsUsed':
			$this->removeInstances();
			(new Configuration())->query()->update(['isUsed' => '0']);
			$config = (new Configuration())->where('id', $_GET['config'])->update(['isUsed' => '1']);
			Customer::truncate();
			Logs::create(['action' => 'SetAsUsed', 'request' => 'id:' . $_GET['config'], 'response' => $config]);
			echo json_encode($config);
			break;
		case 'getAPIToken':
			$ini = parse_ini_file(__DIR__ . '/../api.ini');
			echo json_encode($ini['apitoken']);
			break;
		default:
			echo json_encode([]);
			break;
		}
		die();
	}
	public function removeInstances() {
		$config = (new Configuration())->where('isUsed', '1')->first();

		$helper = new AccountHelper('', $config);

		$connection = $helper->connectToSSH();

		if (is_array($connection) || !$connection) {
			//return 'Unable to connect to SSH';
		} else {
			$helper->killByName();
		}
	}

	public function testConnection() {
		$host = json_decode(json_encode($_GET), FALSE);
		$helper = new AccountHelper('testuser', $host);
		$connection = $helper->connectToSSH();
		if (is_array($connection) || !$connection) {
			return 'Unable to connect to SSH';
		}

		$helper->createUserDir();
		$helper->createTinocacheConfig();
		$pass = $helper->setSaslDBFile();
		$helper->changeOwner();
		$helper->exportVar();
		$port = $helper->runTinocache();
		$helper->savePidToFile();
		try
		{
			$testResult = $helper->testServer('testuser', $pass, $port);

		} catch (\Exception $ex) {
			return $ex->getMessage() . '-' . print_r($ex->getTrace(), true);
		} finally {
			$helper->killByPID();
			$helper->removeTinocacheDB();
		}
		if ($testResult) {
			return $testResult;
		} else {
			return 'Connection successfull, but couldn\'t connect to Tinocache instance';
		}
	}

	/**
	 *
	 * @param Request $request
	 */
	public function index(Request $request) {

		if (isset($_POST['ServerAddr']) && isset($_POST['ServerPort']) && isset($_POST['ServerUsername'])) {
			if (isset($_POST['id'])) {
				$action = 'Edit Server';
				$id = filter_input(INPUT_POST, 'id');
			} else {
				$action = 'Add Server';
			}
			//Logs::create(['action' => $action, 'request' => (isset($id) ? "ID: $id\n" : ''). "Server: ".$_POST['ServerAddr']."\nPort: ".$_POST['ServerPort']."\nUsername: ".$_POST['ServerUsername']."\nPass: ".$_POST['ServerPassword'], 'response' => 'Success']);
			$this->saveSettings($request);
			$this->view->alert['success'] = 'Settings saved!';
		} elseif (filter_input(INPUT_POST, 'action')) {
			$this->view->alert = $this->{filter_input(INPUT_POST, 'action')}($request);
		}

		$this->view->settings = Configuration::get()->keyBy('id');
	}

	public function save(Request $request) {

	}

	/**
	 * Save new package configurations
	 * @param Request $request
	 */
	private function saveToken(Request $request) {

		$return = file_put_contents(__DIR__ . '/../api.ini', 'apitoken="' . $request->get('TokenInput') . '"');
		return ['success' => 'Token saved'];
	}
	private function ajaxAction($data) {
		$results = array();

		echo json_encode($results);
		die();
	}

	/**
	 * Save setting
	 * @param Request $request
	 */
	public function saveSettings(Request $request) {
		if ($request->get('id') == "") {
			$settings = \tinocachePlugin\Model\Db\Table\Configuration::instance();
		} else {

			$settings = \tinocachePlugin\Model\Db\Table\Configuration::where('id', $request->get('id'))->first();
		}

		foreach ($request->all() as $key => $value) {
			if ($key == 'id' OR $key == 'isUsed') {
				continue;
			}

			if ($key == 'ServerPassword' && $value == '') {
				continue;
			}

			$settings->$key = $value;
		}
		$settings->isUsed = $settings->isUsed ? $settings->isUsed : 0;
		$settings->disableSASL = $request->get('disableSASL') ? $request->get('disableSASL') : 0;

		$settings->save();
	}
}

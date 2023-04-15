<?php
namespace tinocachePlugin\Model\Hook\Action;

use Exception;
use tinocachePlugin\Model\Api\cWatchApi\APIController;
use tinocachePlugin\Model\Api\Whm\Functions as WhmApi;
use tinocachePlugin\Model\Db\Table\DNSRecord;
use tinocachePlugin\Model\Db\Table\Domains;
use tinocachePlugin\Model\Tool\Logger;

class Terminate implements AbstractHook
{

    public function execute($input = '')
    {
        try {
            APIController::inst('Account')->close(['username' => $input['user']]);
            $whmAccount = ( new WhmApi\AccountDetails())->request(['user' => $input['user']]);
            $domain = Domains::find($whmAccount->domain);
            $domain->delete();
            $dnsRecord = DNSRecord::find($input['user']);
            $dnsRecord->delete();
        } catch (Exception $ex) {
            Logger::debug("", __CLASS__, json_encode($input), json_encode($ex));
        }
    }
}

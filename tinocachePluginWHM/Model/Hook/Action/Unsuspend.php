<?php
namespace tinocachePlugin\Model\Hook\Action;

use Exception;
use tinocachePlugin\Model\Api\cWatchApi\APIController;
use tinocachePlugin\Model\Api\Whm\Functions\AccountDetails;
use tinocachePlugin\Model\Api\Whm\Functions\EditZoneRecord;
use tinocachePlugin\Model\Db\Table\DNSRecord;
use tinocachePlugin\Model\Tool\Logger;

class Unsuspend implements AbstractHook
{

    public function execute($input = '')
    {
        $user = $input['args']['user'];
        try {
            $login = APIController::inst('Account')->checkLogin(['username' => $user]);
            if ($login->data->available == 0) {
                $login = APIController::inst('Account')->resume(['login' => $user]);
                $accountDetails = ( new AccountDetails())->request(['user' => $user]);
                $this->setRecordBack($accountDetails->domain);
            }
        } catch (Exception $ex) {
            Logger::debug("", __CLASS__, json_encode($input), json_encode($ex));
        }
    }

    private function setRecordBack($domain)
    {
        try {
            $record = DNSRecord::find($domain);
            if ($record) {
                $zone = new EditZoneRecord();
                $zone->domain = $record->domain;
                $zone->line = $record->line;
                $zone->name = $record->name;
                $zone->ttl = $record->ttl;
                $zone->class = $record->class;
                $zone->type = $record->type;

                $resellers = APIController::inst('Reseller')->getAll();
                foreach ($resellers->data->resellers as $reseller) {
                    if ($reseller->master_reseller == null) {
                        $resellerDomains = $reseller->domains;
                    }
                }

                $zone->address = gethostbyname($resellerDomains[0]);

                $zone->request();
            }
        } catch (Exception $ex) {
            Logger::debug("", __CLASS__, __FUNCTION__, json_encode($ex));
        }
    }
}

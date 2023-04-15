<?php
namespace tinocachePlugin\Model\Hook\Action;

use Exception;
use tinocachePlugin\Model\Api\cWatchApi\APIController;
use tinocachePlugin\Model\Api\Whm\Functions\AccountDetails;
use tinocachePlugin\Model\Api\Whm\Functions\DumpZone;
use tinocachePlugin\Model\Api\Whm\Functions\EditZoneRecord;
use tinocachePlugin\Model\Db\Table\Configurations;
use tinocachePlugin\Model\Db\Table\DNSRecord;
use tinocachePlugin\Model\Db\Table\cWatchUser;
use tinocachePlugin\Model\Tool\Logger;

class Create implements AbstractHook
{

    public function execute($input = '')
    {
        try {
            $user = $input['user'];
            $email = $input['contactemail'];
            $domain = $input['domain'];

            $login = APIController::inst('Account')->checkLogin(['username' => $user]);
            $accountDetails = ( new AccountDetails())->request(['user' => $user]);
            $configuration = Configurations::find($input['plan']);
            if (isset($configuration->plan) && !empty($configuration->plan)) {
                if ($login->data->available != 0) {
                    $password = $this->generatePassword();
                    // Create account
                    $response = APIController::inst('Account')->open([
                        'username' => $user,
                        'email' => $email,
                        'name' => $user,
                        'domain' => $domain,
                        'password' => $password
                    ]);

                    if ($response->return_code != "FAILURE") {
                        APIController::inst('Billing')->startSubscription([
                            'login' => $user,
                            'package_code' => $configuration->plan,
                            'start_date' => rawurlencode(date("Y-m-d H:i:s")),
                            'price' => 0,
                        ]);

                        $webForceUser = cWatchUser::firstOrNew(['userid' => $accountDetails->uid]);
                        $webForceUser->userid = $accountDetails->uid;
                        $webForceUser->domain = $domain;
                        $webForceUser->password = $password;
                        $webForceUser->save();

                        APIController::inst('Site')->setDomains(['login' => $user, 'domains' => $domain]);

                        // Add info about zone to database and set dns record pointing to domain ip
                        $zoneInfo = $this->getRecordInfo($domain);
                        $dnsrecord = DNSRecord::firstOrNew(['id' => $domain]);
                        $dnsrecord->id = $domain;
                        $dnsrecord->domain = $domain;
                        $dnsrecord->line = $zoneInfo->Line;
                        $dnsrecord->name = $zoneInfo->name;
                        $dnsrecord->class = $zoneInfo->class;
                        $dnsrecord->ttl = $zoneInfo->ttl;
                        $dnsrecord->type = $zoneInfo->type;
                        $dnsrecord->address = $zoneInfo->address;
                        $dnsrecord->save();

                        $this->setRecord($domain, $zoneInfo->Line, $zoneInfo->name, $zoneInfo->class, $zoneInfo->ttl, $zoneInfo->type);
                    }
                }
            }
        } catch (Exception $ex) {
            Logger::debug("", __CLASS__, json_encode($input), json_encode($ex));
        }
    }

    private function generatePassword()
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        return substr(str_shuffle($chars), 0, 16);
    }

    private function getRecordInfo($domain)
    {
        $zone = new DumpZone();
        $zone->domain = $domain;
        $records = $zone->request();

        foreach ($records as $record) {
            if (!isset($record->name)) {
                continue;
            }

            if (stripos($record->name, $domain) !== false && stripos($record->name, '.' . $domain) === false && $record->type == "A") {
                return $record;
            }
        }
    }

    private function setRecord($domain, $line, $name, $class, $ttl, $type)
    {
        $zone = new EditZoneRecord();
        $zone->domain = $domain;
        $zone->line = $line;
        $zone->name = $name;
        $zone->ttl = $ttl;
        $zone->class = $class;
        $zone->type = $type;

        $resellers = APIController::inst('Reseller')->getAll();
        foreach ($resellers->data->resellers as $reseller) {
            if ($reseller->master_reseller == null) {
                $resellerDomains = $reseller->domains;
            }
        }

        $zone->address = gethostbyname($resellerDomains[0]);

        $zone->request();
    }
}

<?php
namespace tinocachePlugin\Model\Hook\Action;

use tinocachePlugin\Model\Db\Table\Configurations;
use tinocachePlugin\Model\Db\Table\Domains;
use tinocachePlugin\Model\Api\Whm\Functions as WhmApi;
use tinocachePlugin\Model\Api\cWatchApi\APIController;

class ChangePackage implements AbstractHook
{

    public function execute($input = '')
    {

        try {
            $user = $input['user'];
            if (empty($user)) {
                return;
            }

            $domain = (new WhmApi\AccountDomain())->request(['user' => $user]);

            $configuration = Configurations::find($input['new_pkg']);

            if (isset($configuration->plan) && !empty($configuration->plan)) {

                if ($configuration->plan != $domain->plan) {
                    APIController::inst('Account')->update([
                        'login' => $user,
                        'package_code' => $configuration->plan,
                        'collection_design_id' => 'skyline3'
                    ]);
                } else {
                    APIController::inst('Billing')->startSubscription([
                        'login' => $user,
                        'package_code' => $configuration->plan,
                        'start_date' => rawurlencode(date("Y-m-d H:i:s")),
                        'price' => 0,
                    ]);
                }

                APIController::inst('Site')->setDomains(['login' => $user, 'domains' => $domain]);
                $newDomain = Domains::findOrNew($domain->domain);

                $newDomain->fill([
                    'package' => $domain->plan,
                    'plan' => $configuration->plan,
                    'domain' => $domain->domain,
                    'user' => $domain->user,
                    'subid' => $domain->uid,
                ])->save();
            } else {
                APIController::inst('Account')->close(['username' => $input['user']]);
            }
        } catch (Exception $ex) {
            \tinocachePlugin\Model\Tool\Logger::debug("", __CLASS__, json_encode($input), json_encode($ex));
        }
    }
}

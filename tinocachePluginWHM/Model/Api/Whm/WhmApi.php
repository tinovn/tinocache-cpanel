<?php

namespace tinocachePlugin\Model\Api\Whm;

use tinocachePlugin\Model\Api\AbstractApi;
use tinocachePlugin\Model\Api\Whm\Factory\ValidatorFactory;
use tinocachePlugin\Model\Db\Table\Settings;

abstract class WhmApi extends AbstractApi
{

   // protected $user;

    public function __construct()
    {
        parent::__construct(
            new WhmDetailsProvider(), ValidatorFactory::createResponseValidator(), new WhmCurl()
        );

        //  $this->fetchWhmUser();
    }
//    private function fetchWhmUser()
//    {
//        $settings   = Settings::get();
//        $this->user = $settings->whmUser;
//    }
}

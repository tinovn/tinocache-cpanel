<?php

namespace tinocachePlugin\Model\Api\Whm;

use tinocachePlugin\Model\Api\AbstractDetailsProvider;

class WhmDetailsProvider extends AbstractDetailsProvider
{

    private $authkey;
    private $endpoint;
    private $user;
    private $pass;

    public function __construct()
    {
        $data = parse_ini_file(dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'api.ini'); 
        $this->authkey  =  $data['apitoken'];
        $this->endpoint = 'https://127.0.0.1:2087';
        $this->user     = '';
        $this->pass     = '';
    }

    public function getAuthkey()
    {
        return $this->authkey;
    }

    public function getEndpoint()
    {
        return $this->endpoint;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getPass()
    {
        return $this->pass;
    }
}

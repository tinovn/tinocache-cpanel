<?php

namespace tinocachePlugin\Model\Api;

abstract class AbstractDetailsProvider
{
    abstract function getAuthkey();
    
    abstract function getEndpoint();
}
<?php

namespace TicketNetwork\Api;

use Config;
use SoapClient;

//app/libraries/TicketNetwork/Api/TicketNetwork.php


class TicketNetwork implements TicketNetworkInterface
{

    private $_wsdl;
    private $_websiteId;

    public function __construct($config = 'ticketnetwork.tnProdData')
    {
        $loadedConfig = Config::get($config);
        $this->_wsdl = $loadedConfig['path'];
        $this->_websiteId = $loadedConfig['websiteConfigID'];
        return $this;
    }

    public function drop()
    {
        return "OK";
    }

    public function run($method = null, $params = array())
    {
        if (!isset($params['websiteConfigId'])):
            $params['websiteConfigID'] = $this->_websiteId;
        endif;
        $client = new SoapClient($this->_wsdl);
        return $client->__soapCall($method, ['parameters' => $params]);

    }
}

?>
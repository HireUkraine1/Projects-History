<?php

namespace App\Statistic;

abstract class ClientAbstract
{
    /**
     * @var
     */
    protected $client;

    /**
     * @var
     */
    protected $service;

    /**
     * ClientAbstract constructor.
     */
    public function __construct()
    {
        $this->setService();
        $this->client = $this->getClient();
    }

    /**
     * @return bool
     */
    public function getClient()
    {
        if (!$this->service) {
            return false;
        }
        if (!$this->client) {
            return \App::make('aws')->createClient($this->service);
        }
        return $this->client;
    }

    /**
     * @return mixed
     */
    abstract protected function setService();

    /**
     * @return mixed
     */
    abstract function getList();
}
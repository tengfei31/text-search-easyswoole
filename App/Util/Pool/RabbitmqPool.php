<?php
namespace App\Util\Pool;

use EasySwoole\Pool\AbstractPool;
use EasySwoole\Pool\Config;

class RabbitmqPool extends AbstractPool 
{
    protected $rabbitmqConfig;

    public function __construct(Config $config)
    {
        parent::__construct($config);
        $this->rabbitmqConfig = $config;
    }

    protected function createObject()
    {
        $connect = new Rabbitmq($this->rabbitmqConfig);
        $connect->connect();
        return $connect;
    }
}
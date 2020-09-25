<?php 
namespace App\Util\Pool;

use EasySwoole\Pool\ObjectInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class Rabbitmq implements ObjectInterface
{
    /**
     * 是否连接
     *
     * @var bool
     */
    public $isConn = false;

    /**
     * 是否在使用
     *
     * @var bool
     */
    public $isUse = false;

    /**
     * 连接符
     *
     * @var AMQPStreamConnection
     */
    public $conn;

    /**
     * rabbitmq配置
     *
     * @var RabbitmqConfig
     */
    public $config;

    public function __construct(RabbitmqConfig $conf)
    {
        $this->config = $conf;
        $this->conn = new AMQPStreamConnection($this->config->getHost(), $this->config->getPort(), $this->config->getUser(), $this->config->getPass(), $this->config->getVhost());
        $this->isConn = true;       
    }

    // public function connect()
    // {
    //     $this->conn = new AMQPStreamConnection($this->config->getHost(), $this->config->getPort(), $this->config->getUser(), $this->config->getPass(), $this->config->getVhost());
    //     $this->setIsUse(false);
    // }

    /**
     * unset 的时候执行
     *
     * @return void
     */
    public function gc()
    {
        if ($this->conn) {
            $this->conn->close();
        }
        $this->isConn = false;
        $this->setIsUse(false);
    }

    /**
     * 使用后,free的时候会执行
     *
     * @return void
     */
    public function objectRestore()
    {
        $this->isConn = true;
        $this->setIsUse(false);
    }

    /**
     * 使用前调用,当返回true，表示该对象可用。返回false，该对象失效，需要回收
     *
     * @return bool|null
     */
    public function beforeUse(): ?bool
    {
        if ($this->isUse) {
            return false;
        }
        return true;
    }

    public function setIsUse(bool $use) :self
    {
        $this->isUse = $use;
        return $this;
    }

    public function getIsUse() :bool
    {
        return $this->isUse;
    }
}
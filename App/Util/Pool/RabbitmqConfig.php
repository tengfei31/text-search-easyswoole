<?php
namespace App\Util\Pool;

use EasySwoole\Pool\Config;
use EasySwoole\Spl\SplBean;

class RabbitmqConfig extends Config
{
    /**
     * host
     *
     * @var string
     */
    public $host = "127.0.0.1";

    /**
     * 端口
     *
     * @var string
     */
    public $port = "5673";

    /**
     * 连接用户名
     *
     * @var string
     */
    public $user = "guest";

    /**
     * 连接密码
     *
     * @var string
     */
    public $pass = "guest";

    /**
     * 连接的虚拟机
     *
     * @var string
     */
    public $vhost = "tengfei";

    public function __construct(array $conf)
    {
        $this->setHost($conf["host"]);
        $this->setPort($conf["port"]);
        $this->setUser($conf["user"]);
        $this->setPass($conf["pass"]);
        $this->setVhost($conf["vhost"]);
    }

    public function setHost(string $host) :self
    {
        $this->host = $host;
        return $this;
    }

    public function getHost() :string
    {
        return $this->host;
    }

    public function setPort(string $port) :self
    {
        $this->port = $port;
        return $this;
    }

    public function getPort() :string
    {
        return $this->port;
    }

    public function setUser(string $user) :self
    {
        $this->user = $user;
        return $this;
    }

    public function getUser() :string
    {
        return $this->user;
    }

    public function setPass(string $pass) :self
    {
        $this->pass = $pass;
        return $this;
    }

    public function getPass() :string
    {
        return $this->pass;
    }

    public function setVhost(string $vhost) :self
    {
        $this->vhost = $vhost;
        return $this;
    }

    public function getVhost() :string
    {
        return $this->vhost;
    }

    public function __set(string $key, $value)
    {
        $this->$key = $value;
        return $this;
    }

    public function __get($key)
    {
        return $this->$key;
    }
}
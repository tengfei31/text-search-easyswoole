<?php
namespace App\Util\Pool\Register;

use EasySwoole\Component\Singleton;
use EasySwoole\EasySwoole\Config;
use EasySwoole\ORM\Db\Config as DbConfig;
use EasySwoole\ORM\Db\Connection;
use EasySwoole\ORM\DbManager;
use EasySwoole\Redis\Config\RedisConfig;
use EasySwoole\RedisPool\Redis;

class RegisterPool 
{
    use Singleton;
    /**
     * 注册各类连接池
     *
     * @return void
     */
    public function register()
    {
        $this->redis();
        $this->mysql();
        $this->rabbitmq();
        $this->ealsticsearch();
    }

    /**
     * 注册redis连接池
     *
     * @return void
     */
    public function redis() 
    {
        $redisPool = Redis::getInstance()->register("redis", new RedisConfig(Config::getInstance()->getConf("REDIS")));
        // 配置
        // $redisPool->setMinObjectNum(20);
        // $redisPool->setMaxObjectNum(40);
    }

    /**
     * 注册mysql连接池
     *
     * @return void
     */
    public function mysql()
    {
        $config = new DbConfig(Config::getInstance()->getConf("MYSQL"));
        DbManager::getInstance()->addConnection(new Connection($config));
    }

    /**
     * 注册rabbitmq连接池
     *
     * @return void
     */
    public function rabbitmq()
    {
        //注册rabbitmq连接池
        // $rabbitmqConf = new RabbitmqConfig(Config::getInstance()->getConf("RABBITMQ"));
        // Manager::getInstance()->register(new RabbitmqPool($rabbitmqConf), "rabbitmq");
    }

    /**
     * 注册elasticsearch连接池
     *
     * @return void
     */
    public function ealsticsearch(){}
}
<?php
namespace App\Command\Rabbitmq;

use EasySwoole\Component\Process\Config;
use EasySwoole\EasySwoole\Command\CommandInterface;
use App\Process\ConsumerProcess;
use EasySwoole\Component\Process\Manager;
use EasySwoole\EasySwoole\Logger;
use Exception;

class Consumer implements CommandInterface
{
    /**
     * 命令名字
     *
     * @var string
     */
    private $commandName = "rabbitmq:consumer";

    /**
     * 命令名字
     *
     * @return string
     */
    public function commandName(): string
    {
        return $this->commandName;
    }
    
    /**
     * 帮助信息
     *
     * @param array $args
     *
     * @return string|null
     */
    public function help(array $args): ?string
    {
        return "消费rabbitmq";
    }

    /**
     * 具体执行内容
     *
     * @param array $args
     *
     * @return string|null
     */
    public function exec(array $args): ?string
    {
        try {
            //注册进程去消费
            $processConfig = new Config();
            $processConfig->setProcessName('rabbitmqConsumerProcess');//设置进程名称
            $processConfig->setProcessGroup('rabbitmqGroup');//设置进程组
            $processConfig->setArg(['a' => 123]);//传参
            $processConfig->setRedirectStdinStdout(false);//是否重定向标准io
            $processConfig->setPipeType($processConfig::PIPE_TYPE_SOCK_DGRAM);//设置管道类型
            $processConfig->setEnableCoroutine(true);//是否自动开启协程
            $processConfig->setMaxExitWaitTime(3);//最大退出等待时间
            Manager::getInstance()->addProcess(new ConsumerProcess($processConfig));
            Manager::getInstance()->addProcess(new ConsumerProcess($processConfig));
            Manager::getInstance()->addProcess(new ConsumerProcess($processConfig));
            
            return NULL;
        } catch (Exception $e) {
            Logger::getInstance()->info(sprintf("consumer错误：%s\n", $e->getMessage()));
            return NULL;
        }
    }



}

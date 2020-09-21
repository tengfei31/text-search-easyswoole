<?php
namespace App\Process;

use EasySwoole\Component\Singleton;
use EasySwoole\Component\Process\Config;
use EasySwoole\Component\Process\Manager;
use App\Process\Rabbitmq\ConsumerProcess;
use EasySwoole\EasySwoole\Config as EasySwooleConfig;

class CustomProcess {
    use Singleton;

    private $workerSetting;

    /**
     * 自定义参数
     *
     * @param int $workerNum
     */
    public function __construct(int $workerNum = 0)
    {
        $this->workerSetting = EasySwooleConfig::getInstance()->getConf("MAIN_SERVER.SETTING");
        if ($workerNum) {
            $this->workerSetting["worker_num"] = $workerNum;
        }
    }

    public function run()
    {
        $this->rabbitmq();
    }

    /**
     * 自定义rabbitmq消费进程
     *
     * @return void
     */
    public function rabbitmq()
    {
        $processConfig = new Config();
        $processConfig->setProcessName('rabbitmqConsumerProcess');//设置进程名称
        $processConfig->setProcessGroup('rabbitmqGroup');//设置进程组
        $processConfig->setArg([]);//传参
        $processConfig->setRedirectStdinStdout(false);//是否重定向标准io
        $processConfig->setPipeType($processConfig::PIPE_TYPE_SOCK_DGRAM);//设置管道类型
        $processConfig->setEnableCoroutine(true);//是否自动开启协程
        $processConfig->setMaxExitWaitTime(3);//最大退出等待时间
        for ($i = 0; $i < $this->workerSetting["worker_num"]; $i++) {
            Manager::getInstance()->addProcess(new ConsumerProcess($processConfig));
        }
    }



}
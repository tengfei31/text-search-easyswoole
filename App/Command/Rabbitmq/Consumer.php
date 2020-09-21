<?php
namespace App\Command\Rabbitmq;

use EasySwoole\Component\Process\Config;
use EasySwoole\EasySwoole\Command\CommandInterface;
use App\Process\Rabbitmq\ConsumerProcess;
use App\Task\Rabbitmq\ConsumerTask;
use EasySwoole\Component\Process\Manager;
use EasySwoole\EasySwoole\Config as EasySwooleConfig;
use EasySwoole\EasySwoole\Logger;
use EasySwoole\EasySwoole\Task\TaskManager;
use EasySwoole\Task\Config as TaskConfig;
use EasySwoole\Task\Task;
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
            $taskConf = EasySwooleConfig::getInstance()->getConf("MAIN_SERVER.TASK");
            $taskConfig = new TaskConfig($taskConf);
            $task = TaskManager::getInstance($taskConfig);
            //task进程去消费
            go(function() use ($task) {
                $task->async(new ConsumerTask);
            });
            go(function() use ($task) {
                $task->async(new ConsumerTask);
            });
            
            return NULL;
        } catch (Exception $e) {
            Logger::getInstance()->info(sprintf("consumer错误：%s\n", $e->getMessage()));
            return NULL;
        }
    }



}

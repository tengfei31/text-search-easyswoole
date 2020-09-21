<?php
namespace App\Task\Rabbitmq;

use EasySwoole\Task\AbstractInterface\TaskInterface;
use EasySwoole\EasySwoole\Config;
use EasySwoole\EasySwoole\Logger;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use Throwable;

class ConsumerTask implements TaskInterface 
{
    /**
     * 队列名字
     *
     * @var string
     */
    public $queue = "test";

    /**
     * 交换机名字
     *
     * @var string
     */
    public $exchange = "router";

    /**
     * 消费标签
     *
     * @var string
     */
    public $consumerTag = "consumer";

    /**
     * @var AMQPStreamConnection
     */
    public $conn;

    /**
     * @var \PhpAmqpLib\Channel\AMQPChannel
     */
    public $channel;

    public function __construct(){}

    public function run(int $taskId, int $workIndex)
    {
        //Logger::getInstance()->info("task_id:{$taskId};work_index:{$workIndex}");
        $conf = Config::getInstance()->getConf("RABBITMQ");
        $this->conn = new AMQPStreamConnection($conf["host"], $conf["port"], $conf["user"], $conf["pass"], $conf["vhost"]);
        $this->channel = $this->conn->channel();
        $this->channel->queue_declare($this->queue, false, true, false, false);
        $this->channel->exchange_declare($this->exchange, AMQPExchangeType::DIRECT, false, true, false);
        $this->channel->queue_bind($this->queue, $this->exchange);

        $this->channel->basic_consume($this->queue, $this->consumerTag, false, false, false, false, [$this, 'process_message']);
        while($this->channel->is_consuming()) {
            $this->channel->wait();
        }
        $this->onShutDown();
    }

    public function onException(Throwable $throwable, int $taskId, int $workIndex)
    {
        //错误就关闭连接
        $this->onShutDown();
    }

    /**
     * 注册一个消费方法
     *
     * @param \PhpAmqpLib\Message\AMQPMessage $message
     *
     * @return void
     */
    public function process_message(AMQPMessage $message)
    {
        Logger::getInstance()->info(sprintf("-------\n%s\n-------", $message->getBody()));
        //printf("\n-------\n%s\n-----------\n", $message->getBody());
        $message->ack();

        if ($message->getBody() == "quit") {
            $message->getChannel()->basic_cancel($message->getConsumerTag());
        }
    }

    /**
     * 进程结束回调
     *
     * @return void
     */
    protected function onShutDown()
    {
        if ($this->conn) {
            $this->conn->close();
        }
        if ($this->channel) {
            $this->channel->close();
        }
    }

    public function __destruct()
    {
        $this->onShutDown();
    }
}
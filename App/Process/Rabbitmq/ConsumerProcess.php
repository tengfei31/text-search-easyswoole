<?php
namespace App\Process\Rabbitmq;

use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\EasySwoole\Config;
use EasySwoole\EasySwoole\Logger;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use Exception;
use Throwable;

class ConsumerProcess extends AbstractProcess 
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

    /**
     * 自定义进程执行方法
     *
     * @param array $arg
     *
     * @return void
     */
    protected function run($arg)
    {
        try {
            Logger::getInstance()->info(sprintf("pid=%d;process_name=%s", $this->getPid(), $this->getProcessName()));
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
            return NULL;
        } catch (Exception $e) {
            printf("consumer错误：%s\n", $e->getMessage());
            return NULL;
        }
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
        //Logger::getInstance()->info(sprintf("\n-------\n%s\n-----------\n", $message->getBody()));
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

    /**
     * 进程异常回调
     *
     * @param \Throwable $throwable
     * @param [type] ...$args
     *
     * @return void
     */
    protected function onException(Throwable $throwable, ...$args)
    {
        //错误就关闭连接
        $this->onShutDown();
    }

    public function __destruct()
    {
        $this->onShutDown();
    }
}
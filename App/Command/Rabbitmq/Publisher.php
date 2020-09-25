<?php
namespace App\Command\Rabbitmq;

use Exception;
use EasySwoole\EasySwoole\Config;
use EasySwoole\EasySwoole\Command\CommandInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;

class Publisher implements CommandInterface
{
    /**
     * 命令名字
     *
     * @var string
     */
    private $commandName = "rabbitmq:publish";

    /**
     * 交换机
     *
     * @var string
     */
    public $exchange = "router";

    /**
     * 队列名字
     *
     * @var string
     */
    public $queue = "tengfei_test";

    /**
     * mq连接
     *
     * @var AMQPStreamConnection
     */
    public $conn;

    /**
     * channel频道
     *
     * @var \PhpAmqpLib\Channel\AMQPChannel
     */
    public $channel;

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
        return "生产rabbitmq";
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

            $conf = Config::getInstance()->getConf("RABBITMQ");
            $this->conn = new AMQPStreamConnection($conf["host"], $conf["port"], $conf["user"], $conf["pass"], $conf["vhost"]);
            

            $this->channel = $this->conn->channel();
            $this->channel->queue_declare($this->queue, false, true, false, false);
            $this->channel->exchange_declare($this->exchange, AMQPExchangeType::DIRECT, false, true, false);
            $this->channel->queue_bind($this->queue, $this->exchange);

            $pid = posix_getpid();
            if ($pid % 2 == 0) {
                //偶数
                $messageBody = sprintf("偶数%d", $pid);
            } else {
                $messageBody = sprintf("奇数%d", $pid);
            }
            $message = new AMQPMessage($messageBody, [
                "content_type" => "text/plain",
                "delivery_mode" => AMQPMessage::DELIVERY_MODE_PERSISTENT,
            ]);
            $this->channel->basic_publish($message, $this->exchange);
            return NULL;
        } catch (Exception $e) {
            printf("publisher错误：%s\n", $e->getMessage());
            return NULL;
        }
    }

    public function shutdown()
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
        $this->shutdown();
    }



}
<?php

namespace App\Util\RabbitMQ;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * RabbitMQ
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class RabbitMQ
{
    /**
     * @var AMQPStreamConnection
     */
    private $connection;

    /**
     * @var AMQPChannel
     */
    private $channel;

    /**
     * @param $url
     */
    public function __construct($url)
    {
        if (!$parsedUrl = parse_url($url)) {
            throw new Exception("Unable to parse the url.");
        }

        $this->connection = new AMQPStreamConnection($parsedUrl['host'], $parsedUrl['port'], $parsedUrl['user'], $parsedUrl['pass']);
        $this->channel = $this->connection->channel();
    }

    public function sendAck(AMQPMessage $AMQPMessage)
    {
        $this->getChannel()->basic_ack($AMQPMessage->delivery_info['delivery_tag']);
    }

    public function listenQueue(string $queue, callable $actionOnMessage)
    {
        $this->getChannel()->basic_qos(null, 1, null);
        $this->getChannel()->basic_consume($queue, '', false, false, false, false, $actionOnMessage);
    }

    public function wait()
    {
        while (count($this->channel->callbacks)) {
            $this->channel->wait();
        }
    }

    /**
     * @return AMQPStreamConnection
     */
    public function getConnection(): AMQPStreamConnection
    {
        return $this->connection;
    }

    /**
     * @return AMQPChannel
     */
    public function getChannel(): AMQPChannel
    {
        return $this->channel;
    }
}
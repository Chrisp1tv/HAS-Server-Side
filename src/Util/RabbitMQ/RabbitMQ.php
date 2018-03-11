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
     * @var string
     */
    private $url;

    /**
     * @var Names
     */
    private $names;

    /**
     * @var AMQPStreamConnection
     */
    private $connection;

    /**
     * @var AMQPChannel
     */
    private $channel;

    /**
     * @param       $url
     * @param Names $names
     */
    public function __construct($url, Names $names)
    {
        $this->url = $url;
        $this->names = $names;
    }

    /**
     * This method should be used only once, with the Setup command. It creates the exchanges, and the queues that HAS
     * needs to work properly.
     */
    public function setUp()
    {
        try {
            $exchangesToDeclare = array(
                $this->getNames()->getRecipientRegistrationExchangeName(),
                $this->getNames()->getDirectCampaignsExchangeName(),
                $this->getNames()->getGroupCampaignsExchangeName(),
                $this->getNames()->getCampaignsStatusExchangeName(),
            );

            $queuesToDeclare = array(
                $this->getNames()->getRecipientRegistrationQueueName() => $this->getNames()->getRecipientRegistrationExchangeName(),
                $this->getNames()->getCampaignsStatusQueueName()       => $this->getNames()->getCampaignsStatusExchangeName(),
            );

            foreach ($exchangesToDeclare as $exchange) {
                $this->getChannel()->exchange_declare($exchange, 'direct', false, true, false);
            }

            foreach ($queuesToDeclare as $queue => $exchange) {
                $this->getChannel()->queue_declare($queue, false, true, false, false);
                $this->getChannel()->queue_bind($queue, $exchange);
            }

            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param AMQPMessage $AMQPMessage
     */
    public function sendAck(AMQPMessage $AMQPMessage)
    {
        $this->getChannel()->basic_ack($AMQPMessage->delivery_info['delivery_tag']);
    }

    /**
     * @param string   $queue
     * @param callable $actionOnMessage
     */
    public function listenQueue(string $queue, callable $actionOnMessage)
    {
        $this->getChannel()->basic_qos(null, 1, null);
        $this->getChannel()->basic_consume($queue, '', false, false, false, false, $actionOnMessage);
    }

    public function wait()
    {
        while (count($this->getChannel()->callbacks)) {
            $this->getChannel()->wait();
        }
    }

    /**
     * @return AMQPStreamConnection
     */
    public function getConnection(): AMQPStreamConnection
    {
        if (null === $this->connection) {
            if (!$parsedUrl = parse_url($this->url)) {
                throw new Exception("Unable to parse the url.");
            }

            $this->connection = new AMQPStreamConnection($parsedUrl['host'], $parsedUrl['port'], $parsedUrl['user'], $parsedUrl['pass']);
        }

        return $this->connection;
    }

    /**
     * @return AMQPChannel
     */
    public function getChannel(): AMQPChannel
    {
        if (null === $this->channel) {
            $this->channel = $this->getConnection()->channel();
        }

        return $this->channel;
    }

    /**
     * @return Names
     */
    public function getNames(): Names
    {
        return $this->names;
    }
}

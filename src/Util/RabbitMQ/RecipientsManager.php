<?php

namespace App\Util\RabbitMQ;

use App\Entity\Recipient;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * RecipientsManager
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class RecipientsManager
{
    /**
     * @var RabbitMQ
     */
    public $rabbitMQ;

    /**
     * @param RabbitMQ $rabbitMQ
     */
    public function __construct(RabbitMQ $rabbitMQ)
    {
        $this->rabbitMQ = $rabbitMQ;
    }

    /**
     * @param Recipient $recipient
     *
     * @return string
     */
    public function createRecipientQueue(Recipient $recipient)
    {
        $newQueueName = $this->rabbitMQ->getNames()->getRecipientQueueName($recipient);
        $this->rabbitMQ->getChannel()->queue_declare($newQueueName, false, true, false, false);
        $this->rabbitMQ->getChannel()->queue_bind($newQueueName, $this->rabbitMQ->getNames()->getDirectCampaignsExchangeName(), $newQueueName);

        return $newQueueName;
    }

    /**
     * @param callable $onRequestAction
     */
    public function startRegistrationRpcServer(callable $onRequestAction)
    {
        $this->rabbitMQ->listenQueue($this->rabbitMQ->getNames()->getRecipientRegistrationQueueName(), $onRequestAction);
        $this->rabbitMQ->wait();
    }

    /**
     * @param AMQPMessage $request
     * @param             $recipientInformation
     */
    public function sendRegistrationResponse(AMQPMessage $request, $recipientInformation)
    {
        $response = new AMQPMessage(json_encode($recipientInformation), array('correlation_id' => $request->get('correlation_id')));

        $this->rabbitMQ->getChannel()->basic_publish($response, '', $request->get('reply_to'));
        $this->rabbitMQ->getChannel()->basic_ack($request->delivery_info['delivery_tag']);
    }
}

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
     * @var Names
     */
    private $names;

    public function __construct(RabbitMQ $rabbitMQ, Names $names)
    {
        $this->rabbitMQ = $rabbitMQ;
        $this->names = $names;
    }

    public function createRecipientQueue(Recipient $recipient)
    {
        $newQueueName = $this->names->getRecipientQueueName($recipient);
        $this->rabbitMQ->getChannel()->queue_declare($newQueueName, false, true, false, false);
        $this->rabbitMQ->getChannel()->queue_bind($newQueueName, $this->names->getDirectCampaignsExchangeName(), $newQueueName);

        return $newQueueName;
    }

    public function startRegistrationRpcServer(callable $onRequestAction)
    {
        $this->rabbitMQ->listenQueue($this->names->getRecipientRegistrationQueueName(), $onRequestAction);
        $this->rabbitMQ->wait();
    }

    public function sendRegistrationResponse(AMQPMessage $request, $recipientInformation)
    {
        $response = new AMQPMessage(json_encode($recipientInformation), array('correlation_id' => $request->get('correlation_id')));

        $this->rabbitMQ->getChannel()->basic_publish($response, '', $request->get('reply_to'));
        $this->rabbitMQ->getChannel()->basic_ack($request->delivery_info['delivery_tag']);
    }
}
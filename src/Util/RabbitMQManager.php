<?php

namespace App\Util;

use App\Entity\Campaign;
use App\Entity\Message;
use App\Entity\Recipient;
use App\Entity\RecipientGroup;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * RabbitMQManager
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class RabbitMQManager
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
     * @var string
     */
    private $clientQueuesPrefix;

    /**
     * @var string
     */
    private $groupExchangeBindsPrefix;

    /**
     * @var string
     */
    private $recipientRegistrationQueueName;

    /**
     * @var string
     */
    private $directCampaignsExchangeName;

    /**
     * @var string
     */
    private $groupCampaignsExchangeName;

    /**
     * @var string
     */
    private $campaignsStatusQueueName;

    /**
     * @param $url
     * @param $clientQueuesPrefix
     * @param $groupExchangeBindsPrefix
     * @param $recipientRegistrationQueueName
     * @param $directCampaignsExchangeName
     * @param $groupCampaignsExchangeName
     * @param $campaignsStatusQueueName
     */
    public function __construct($url, $clientQueuesPrefix, $groupExchangeBindsPrefix, $recipientRegistrationQueueName, $directCampaignsExchangeName, $groupCampaignsExchangeName, $campaignsStatusQueueName)
    {
        if (!$parsedUrl = parse_url($url)) {
            throw new Exception("Unable to parse the url.");
        }

        $this->connection = new AMQPStreamConnection($parsedUrl['host'], $parsedUrl['port'], $parsedUrl['user'], $parsedUrl['pass']);
        $this->channel = $this->connection->channel();
        $this->clientQueuesPrefix = $clientQueuesPrefix;
        $this->groupExchangeBindsPrefix = $groupExchangeBindsPrefix;
        $this->recipientRegistrationQueueName = $recipientRegistrationQueueName;
        $this->directCampaignsExchangeName = $directCampaignsExchangeName;
        $this->groupCampaignsExchangeName = $groupCampaignsExchangeName;
        $this->campaignsStatusQueueName = $campaignsStatusQueueName;
    }


    public function createRecipientQueue(Recipient $recipient)
    {
        $newQueueName = $this->getRecipientQueueName($recipient);
        $this->channel->queue_declare($newQueueName, false, true, false, false);
        $this->channel->queue_bind($newQueueName, $this->directCampaignsExchangeName, $newQueueName);

        return $newQueueName;
    }

    public function startRegistrationRpcServer(callable $onRequestAction)
    {
        $this->channel->basic_qos(null, 1, null);
        $this->channel->basic_consume($this->recipientRegistrationQueueName, '', false, false, false, false, $onRequestAction);
        $this->wait();
    }

    public function sendRegistrationResponse(AMQPMessage $request, $recipientInformation)
    {
        $response = new AMQPMessage($this->encodeRegistrationResponse($recipientInformation), array('correlation_id' => $request->get('correlation_id')));

        $request->delivery_info['channel']->basic_publish($response, '', $request->get('reply_to'));
        $request->delivery_info['channel']->basic_ack($request->delivery_info['delivery_tag']);
    }

    public function sendCampaign(Campaign $campaign)
    {
        $recipients = $campaign->getRecipients()->toArray();
        $recipientGroups = $campaign->getRecipientGroups();
        $AMQPMessage = new AMQPMessage($this->encodeMessage($campaign->getMessage()));

        if (1 < $recipientGroups->count()) {
            /** @var RecipientGroup $recipientGroup */
            foreach ($recipientGroups->getIterator() as $recipientGroup) {
                $recipients = array_merge($recipients, $recipientGroup->getRecipients()->toArray());
            }

            $recipients = array_unique($recipients, SORT_REGULAR);
        } elseif (1 === $recipientGroups->count()) {
            $this->channel->basic_publish($AMQPMessage, $this->groupCampaignsExchangeName, $this->getGroupBindKey($recipientGroups->first()));
            $recipients = array_diff($recipients, $recipientGroups->first()->getRecipients()->toArray());
        }

        foreach ($recipients as $recipient) {
            $this->channel->basic_publish($AMQPMessage, $this->directCampaignsExchangeName, $this->getRecipientQueueName($recipient));
        }
    }

    public function listenCampaignsStatus(callable $onRequestAction)
    {
        $this->channel->basic_qos(null, 1, null);
        $this->channel->basic_consume($this->campaignsStatusQueueName, '', false, false, false, false, $onRequestAction);
        $this->wait();
    }

    public function confirmMessageStatusProcessing(AMQPMessage $AMQPMessage)
    {
        $AMQPMessage->delivery_info['channel']->basic_ack($AMQPMessage->delivery_info['delivery_tag']);
    }

    public function updateRecipientGroupBindings(RecipientGroup $recipientGroup, array $oldGroupRecipients, array $newGroupRecipients)
    {
        $addedRecipients = array_diff($newGroupRecipients, $oldGroupRecipients);
        $removedRecipients = array_diff($oldGroupRecipients, $newGroupRecipients);

        foreach ($addedRecipients as $addedRecipient) {
            $this->channel->queue_bind($this->getRecipientQueueName($addedRecipient), $this->groupCampaignsExchangeName, $this->getGroupBindKey($recipientGroup));
        }

        foreach ($removedRecipients as $removedRecipient) {
            $this->channel->queue_unbind($this->getRecipientQueueName($removedRecipient), $this->groupCampaignsExchangeName, $this->getGroupBindKey($recipientGroup));
        }
    }

    private function wait()
    {
        while (count($this->channel->callbacks)) {
            $this->channel->wait();
        }
    }

    private function getRecipientQueueName(Recipient $recipient)
    {
        return $this->clientQueuesPrefix . $recipient->getId();
    }

    private function getGroupBindKey(RecipientGroup $recipientGroup)
    {
        return $this->groupExchangeBindsPrefix . $recipientGroup->getId();
    }

    private function encodeRegistrationResponse(array $recipientInformation)
    {
        return json_encode($recipientInformation);
    }

    private function encodeMessage(Message $message)
    {
        $serializer = new Serializer(array(new ObjectNormalizer()), array(new JsonEncoder()));

        return $serializer->serialize($message, 'json');
    }
}
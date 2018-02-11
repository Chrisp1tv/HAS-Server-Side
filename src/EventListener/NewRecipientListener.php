<?php

namespace App\EventListener;

use App\Entity\Recipient;
use App\Util\RabbitMQManager;
use Doctrine\ORM\EntityManager;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use OldSound\RabbitMqBundle\RabbitMq\Producer;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * NewRecipientListener
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class NewRecipientListener implements ConsumerInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var Producer
     */
    private $directCampaignsProducer;

    /**
     * @var string
     */
    private $clientQueuesPrefix;

    public function __construct(EntityManager $entityManager, Producer $directCampaignsProducer, string $clientQueuesPrefix)
    {
        $this->entityManager = $entityManager;
        $this->directCampaignsProducer = $directCampaignsProducer;
        $this->clientQueuesPrefix = $clientQueuesPrefix;
    }

    public function execute(AMQPMessage $AMQPMessage)
    {
        $newRecipientInformation = json_decode($AMQPMessage);

        if (!property_exists($newRecipientInformation, 'name')) {
            return null;
        }

        $newRecipient = new Recipient();
        $newRecipient->setName($newRecipientInformation->name);

        $this->entityManager->persist($newRecipient);
        $this->entityManager->flush();

        return RabbitMQManager::createRecipientQueue($this->directCampaignsProducer, $this->clientQueuesPrefix, $newRecipient);
    }
}
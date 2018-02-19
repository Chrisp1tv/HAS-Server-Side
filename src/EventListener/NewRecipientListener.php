<?php

namespace App\EventListener;

use App\Entity\Recipient;
use App\Util\RabbitMQManager;
use Doctrine\ORM\EntityManagerInterface;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * NewRecipientListener
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class NewRecipientListener
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var RabbitMQManager
     */
    private $RabbitMQManager;

    public function __construct(EntityManagerInterface $entityManager, RabbitMQManager $RabbitMQManager)
    {
        $this->entityManager = $entityManager;
        $this->RabbitMQManager = $RabbitMQManager;
    }

    public function execute(AMQPMessage $AMQPMessage)
    {
        $newRecipientInformation = json_decode($AMQPMessage->getBody());

        if (!property_exists($newRecipientInformation, 'name')) {
            return null;
        }

        $newRecipient = new Recipient();
        $newRecipient->setName($newRecipientInformation->name);

        $this->entityManager->persist($newRecipient);
        $this->entityManager->flush();
        $this->RabbitMQManager->sendRegistrationResponse($AMQPMessage, $this->RabbitMQManager->createRecipientQueue($newRecipient));
    }
}
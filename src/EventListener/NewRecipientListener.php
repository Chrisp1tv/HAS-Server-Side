<?php

namespace App\EventListener;

use App\Entity\Recipient;
use App\Util\RabbitMQ\RecipientsManager;
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
     * @var RecipientsManager
     */
    private $recipientsManager;

    public function __construct(EntityManagerInterface $entityManager, RecipientsManager $recipientsManager)
    {
        $this->entityManager = $entityManager;
        $this->recipientsManager = $recipientsManager;
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

        $newRecipientInformation = array(
            'queueName' => $this->recipientsManager->createRecipientQueue($newRecipient),
            'id'        => $newRecipient->getId(),
        );

        $this->recipientsManager->sendRegistrationResponse($AMQPMessage, $newRecipientInformation);
    }
}
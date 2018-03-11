<?php

namespace App\EventListener;

use App\Entity\Recipient;
use App\Util\RabbitMQ\RecipientsManager;
use Doctrine\ORM\EntityManagerInterface;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * NewRecipientListener - Listens any recipient registration to save it into the database.
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

    /**
     * @param EntityManagerInterface $entityManager
     * @param RecipientsManager      $recipientsManager
     */
    public function __construct(EntityManagerInterface $entityManager, RecipientsManager $recipientsManager)
    {
        $this->entityManager = $entityManager;
        $this->recipientsManager = $recipientsManager;
    }

    /**
     * @param AMQPMessage $AMQPMessage
     */
    public function execute(AMQPMessage $AMQPMessage)
    {
        $newRecipientInformation = json_decode($AMQPMessage->getBody());

        if (!property_exists($newRecipientInformation, 'name')) {
            return;
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

<?php

namespace App\EventListener;

use App\Entity\ConnectionLogs;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * UserListener - Listen any administrator connection to log into the database.
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class UserListener
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param InteractiveLoginEvent $event
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $administrator = $event->getAuthenticationToken()->getUser();
        $IPConnection = $event->getRequest()->getClientIp();

        $connectionLog = new ConnectionLogs();
        $connectionLog
            ->setAdministrator($administrator)
            ->setIPConnection($IPConnection)
            ->setConnectionDate(new \DateTime());

        $this->entityManager->persist($connectionLog);
        $this->entityManager->flush();
    }
}

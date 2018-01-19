<?php

namespace App\Repository;

use App\Entity\Recipient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * RecipientRepository
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class RecipientRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Recipient::class);
    }

    /**
     * @return int
     */
    public function countAll(): int
    {
        $queryBuilder = $this->createQueryBuilder('recipient')
            ->select('count(recipient.id)');

        return $queryBuilder
            ->getQuery()
            ->getSingleScalarResult();
    }
}
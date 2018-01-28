<?php

namespace App\Repository;

use App\Entity\RecipientGroup;
use App\Util\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * RecipientGroupRepository
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class RecipientGroupRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, RecipientGroup::class);
    }

    public function find($id)
    {
        $queryBuilder = $this->createQueryBuilder('recipientGroup')
            ->join('recipientGroup.recipients', 'recipients')
            ->where('recipientGroup.id = :id')
            ->setParameter('id', $id);

        return $queryBuilder
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllPaginated($itemsPerPage, $page)
    {
        $queryBuilder = $this->createQueryBuilder('recipientGroup')
            ->orderBy('recipientGroup.id', 'DESC');

        return new Paginator($queryBuilder->getQuery(), $itemsPerPage, $page);
    }
}
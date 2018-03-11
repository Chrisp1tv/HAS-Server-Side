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
    /**
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, RecipientGroup::class);
    }

    /**
     * @param mixed $id
     *
     * @return RecipientGroup|null The recipients group identified by the given id, null otherwise
     */
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

    /**
     * @param $itemsPerPage
     * @param $page
     *
     * @return Paginator All the recipients groups, paginated
     */
    public function findAllPaginated($itemsPerPage, $page)
    {
        $queryBuilder = $this->createQueryBuilder('recipientGroup')
            ->orderBy('recipientGroup.id', 'DESC');

        return new Paginator($queryBuilder->getQuery(), $itemsPerPage, $page);
    }
}

<?php

namespace App\Repository;

use App\Entity\Recipient;
use App\Entity\RecipientGroup;
use App\Util\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * RecipientRepository
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class RecipientRepository extends ServiceEntityRepository
{
    /**
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Recipient::class);
    }

    /**
     * @param             $itemsPerPage
     * @param             $page
     * @param null|string $search
     *
     * @return Paginator All the recipients, paginated, and filtered by search if given
     */
    public function findPaginated($itemsPerPage, $page, ?string $search)
    {
        $queryBuilder = $this->createQueryBuilder('recipient')
            ->orderBy('recipient.id', 'DESC');

        if (null != $search) {
            $queryBuilder
                ->where('recipient.name LIKE :search')
                ->orWhere('recipient.linkingIdentifier LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        return new Paginator($queryBuilder->getQuery(), $itemsPerPage, $page);
    }

    /**
     * @param RecipientGroup $recipientGroup
     * @param                $itemsPerPage
     * @param                $page
     *
     * @return Paginator All the recipients, paginated, and who belong to given recipients group
     */
    public function findPaginatedByRecipientGroup(RecipientGroup $recipientGroup, $itemsPerPage, $page)
    {
        $queryBuilder = $this->createQueryBuilder('recipient')
            ->join('recipient.groups', 'rg')
            ->where('rg.id = :recipientGroupId')
            ->setParameter('recipientGroupId', $recipientGroup->getId())
            ->orderBy('recipient.id', 'DESC');

        return new Paginator($queryBuilder->getQuery(), $itemsPerPage, $page);
    }

    /**
     * @return int The number of recipients
     */
    public function countAll()
    {
        $queryBuilder = $this->createQueryBuilder('recipient')
            ->select('count(recipient.id)');

        return $queryBuilder
            ->getQuery()
            ->getSingleScalarResult();
    }
}

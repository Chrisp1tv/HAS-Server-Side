<?php

namespace App\Repository;

use App\Entity\Administrator;
use App\Entity\Campaign;
use App\Util\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * CampaignRepository
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class CampaignRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Campaign::class);
    }

    /**
     * @param Administrator $administrator
     *
     * @return array
     */
    public function findBySender(Administrator $administrator)
    {
        $queryBuilder = $this->createQueryBuilder('campaign')
            ->where('campaign.sender = :sender')
            ->setParameter('sender', $administrator)
            ->orderBy('campaign.sendingDate', 'DESC');

        return $queryBuilder
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @param int $id
     *
     * @return Campaign|null
     */
    public function find($id)
    {
        $queryBuilder = $this->createQueryBuilder('campaign')
            ->join('campaign.sender', 'sender')
            ->leftJoin('campaign.recipients', 'recipients')
            ->leftJoin('campaign.recipientGroups', 'recipientGroups')
            ->where('campaign.id = :id')
            ->setParameter('id', $id);

        return $queryBuilder
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param $itemsPerPage
     * @param $page
     *
     * @return Paginator
     */
    public function findAllPaginated($itemsPerPage, $page)
    {
        $queryBuilder = $this->createQueryBuilder('campaign')
            ->orderBy('campaign.sendingDate', 'DESC');

        return new Paginator($queryBuilder->getQuery(), $itemsPerPage, $page);
    }

    /**
     * @return int
     */
    public function countAll()
    {
        $queryBuilder = $this->createQueryBuilder('campaign')
            ->select('count(campaign.id)');

        return $queryBuilder
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return int
     */
    public function countUnsent()
    {
        $queryBuilder = $this->createQueryBuilder('campaign')
            ->select('count(campaign.id)')
            ->where('campaign.sendingDate > :now')
            ->setParameter('now', new \DateTime());

        return $queryBuilder
            ->getQuery()
            ->getSingleScalarResult();
    }
}
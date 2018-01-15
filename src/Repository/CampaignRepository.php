<?php

namespace App\Repository;

use App\Entity\Campaign;
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
     * @param int $id
     *
     * @return Campaign|null
     */
    public function find($id): ?Campaign
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
}
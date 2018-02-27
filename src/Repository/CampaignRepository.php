<?php

namespace App\Repository;

use App\Entity\Administrator;
use App\Entity\Campaign;
use App\Util\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
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
     * @param               $itemsPerPage
     * @param               $page
     *
     * @return Paginator
     */
    public function findPaginatedByAdministrator(Administrator $administrator, $itemsPerPage, $page)
    {
        $queryBuilder = $this->createQueryBuilder('campaign')
            ->where('campaign.sender = :sender')
            ->setParameter('sender', $administrator)
            ->orderBy('campaign.sendingDate', 'DESC');

        return new Paginator($queryBuilder->getQuery(), $itemsPerPage, $page);
    }

    /**
     * @param mixed     $id
     * @param null|bool $withStatistics
     *
     * @return Campaign|null
     */
    public function find($id, $withStatistics = false)
    {
        $queryBuilder = $this->createQueryBuilder('campaign')
            ->join('campaign.sender', 'sender')
            ->leftJoin('campaign.recipients', 'recipients')->addSelect('recipients')
            ->leftJoin('campaign.recipientGroups', 'recipientGroups')->addSelect('recipientGroups')
            ->leftJoin('recipientGroups.recipients', 'recipientsOfRecipientGroups')->addSelect('recipientsOfRecipientGroups')
            ->where('campaign.id = :id')
            ->setParameter('id', $id);

        if ($withStatistics) {
            $queryBuilder
                ->leftJoin('campaign.receivedBy', 'receivedBy')->addSelect('receivedBy')
                ->leftJoin('campaign.seenBy', 'seenBy')->addSelect('seenBy');
        }

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
            ->select('count(campaign.id)');

        $this->whereUnsent($queryBuilder);

        return $queryBuilder
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return mixed
     */
    public function findUnsent()
    {
        $queryBuilder = $this->createQueryBuilder('campaign')
            ->join('campaign.message', 'message')
            ->leftJoin('campaign.recipientGroups', 'recipientGroups')
            ->leftJoin('campaign.recipients', 'recipients');

        $this->whereUnsent($queryBuilder);

        return $queryBuilder
            ->getQuery()
            ->getResult();
    }

    /**
     * @param QueryBuilder $queryBuilder
     */
    protected function whereUnsent(QueryBuilder $queryBuilder)
    {
        $queryBuilder
            ->andWhere('(campaign.sendingDate <= :now AND campaign.effectiveSendingDate IS NULL) OR (campaign.repetitionFrequency IS NOT NULL AND DATE_ADD(campaign.effectiveSendingDate, campaign.repetitionFrequency, \'MINUTE\') <= campaign.endDate AND DATE_ADD(campaign.effectiveSendingDate, campaign.repetitionFrequency, \'MINUTE\') <= :now)')
            ->setParameter('now', new \DateTime());
    }
}
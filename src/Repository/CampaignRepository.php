<?php

namespace App\Repository;

use App\Entity\Administrator;
use App\Entity\Campaign;
use App\Entity\Recipient;
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

    /**
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Campaign::class);
    }

    /**
     * @param Recipient $recipient
     *
     * @return int The number of campaigns for the given recipient
     */
    public function countByRecipient(Recipient $recipient)
    {
        $queryBuilder = $this->countCampaigns()
            ->join('campaign.recipients', 'recipients', 'WITH', 'recipients.id = :recipientId')
            ->setParameter('recipientId', $recipient->getId());

        return $queryBuilder
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param Recipient $recipient
     *
     * @return int The number of received campaigns for the given recipient
     */
    public function countReceivedByRecipient(Recipient $recipient)
    {
        $queryBuilder = $this->countCampaigns()
            ->join('campaign.receivedBy', 'receivers', 'WITH', 'receivers.id = :recipientId')
            ->setParameter('recipientId', $recipient->getId());

        return $queryBuilder
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param Recipient $recipient
     *
     * @return int The number of read campaigns for the given recipient
     */
    public function countReadByRecipient(Recipient $recipient)
    {
        $queryBuilder = $this->countCampaigns()
            ->join('campaign.seenBy', 'witnesses', 'WITH', 'witnesses.id = :recipientId')
            ->setParameter('recipientId', $recipient->getId());

        return $queryBuilder
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param Recipient $recipient
     *
     * @return Campaign[] The campaigns for the given recipient
     */
    public function findByRecipient(Recipient $recipient)
    {
        $queryBuilder = $this->createQueryBuilder('campaign')
            ->join('campaign.recipients', 'recipients', 'WITH', 'recipients.id = :recipientId')
            ->leftJoin('campaign.receivedBy', 'receivers', 'WITH', 'receivers.id = :recipientId')->addSelect('receivers')
            ->leftJoin('campaign.seenBy', 'witnesses', 'WITH', 'witnesses.id = :recipientId')->addSelect('witnesses')
            ->setParameter('recipientId', $recipient->getId());

        return $queryBuilder
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Administrator $administrator
     * @param               $itemsPerPage
     * @param               $page
     *
     * @return Paginator The paginated campaigns, sent by the given administrator
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
     * @return Campaign|null The campaign for the given id
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
     * @return Paginator All the campaigns, paginated
     */
    public function findAllPaginated($itemsPerPage, $page)
    {
        $queryBuilder = $this->createQueryBuilder('campaign')
            ->orderBy('campaign.sendingDate', 'DESC');

        return new Paginator($queryBuilder->getQuery(), $itemsPerPage, $page);
    }

    /**
     * @return int The number of created campaigns
     */
    public function countAll()
    {
        $queryBuilder = $this->countCampaigns();

        return $queryBuilder
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return int The number of unsent campaigns
     */
    public function countUnsent()
    {
        $queryBuilder = $this->countCampaigns();

        $this->whereUnsent($queryBuilder);

        return $queryBuilder
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return Campaign[] The unsent campaigns
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
     * @return QueryBuilder
     */
    protected function countCampaigns()
    {
        return $this
            ->createQueryBuilder('campaign')
            ->select('count(campaign.id)');
    }

    /**
     * @param QueryBuilder $queryBuilder
     */
    protected function whereUnsent(QueryBuilder $queryBuilder)
    {
        $queryBuilder
            ->andWhere('campaign.sendingDate <= :now AND campaign.effectiveSendingDate IS NULL')
            ->setParameter('now', new \DateTime());
    }
}

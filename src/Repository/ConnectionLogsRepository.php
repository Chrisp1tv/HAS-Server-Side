<?php

namespace App\Repository;

use App\Entity\Administrator;
use App\Entity\ConnectionLogs;
use App\Util\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * ConnectionLogsRepository
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class ConnectionLogsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ConnectionLogs::class);
    }

    public function findConnectionLogsByAdministrator(Administrator $administrator, $maxResults = null)
    {
        $query = $this
            ->findByAdministrator($administrator)
            ->getQuery();

        if (null != $maxResults) {
            $query
                ->setMaxResults($maxResults);
        }

        return $query
            ->getArrayResult();
    }

    public function findPaginatedConnectionLogsByAdministrator(Administrator $administrator, $itemsPerPage, $page)
    {
        $queryBuilder = $this->findByAdministrator($administrator);

        return new Paginator($queryBuilder->getQuery(), $itemsPerPage, $page);
    }

    /**
     * @param Administrator $administrator
     *
     * @return ConnectionLogs
     */
    public function findPenultimateConnectionByAdministrator(Administrator $administrator)
    {
        $queryBuilder = $this->findByAdministrator($administrator);

        return $queryBuilder
            ->getQuery()
            ->setFirstResult(1)
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }

    private function findByAdministrator(Administrator $administrator)
    {
        $queryBuilder = $this->createQueryBuilder('connectionLogs')
            ->where('connectionLogs.administrator = :administrator')
            ->setParameter('administrator', $administrator)
            ->orderBy('connectionLogs.connectionDate', 'DESC');

        return $queryBuilder;
    }
}
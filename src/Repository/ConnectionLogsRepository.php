<?php

namespace App\Repository;

use App\Entity\Administrator;
use App\Entity\ConnectionLogs;
use App\Util\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * ConnectionLogsRepository
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class ConnectionLogsRepository extends ServiceEntityRepository
{
    /**
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ConnectionLogs::class);
    }

    /**
     * @param Administrator $administrator
     * @param int|null      $maxResults
     *
     * @return ConnectionLogs[] The connection logs for the given administrator, limited by maxResults
     */
    public function findConnectionLogsByAdministrator(Administrator $administrator, ?int $maxResults)
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

    /**
     * @param Administrator $administrator
     * @param               $itemsPerPage
     * @param               $page
     *
     * @return Paginator The paginated connection logs for the given administrator
     */
    public function findPaginatedConnectionLogsByAdministrator(Administrator $administrator, $itemsPerPage, $page)
    {
        $queryBuilder = $this->findByAdministrator($administrator);

        return new Paginator($queryBuilder->getQuery(), $itemsPerPage, $page);
    }

    /**
     * @param Administrator $administrator
     *
     * @return ConnectionLogs The penultimate connection of the given administrator
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

    /**
     * @param Administrator $administrator
     *
     * @return QueryBuilder
     */
    protected function findByAdministrator(Administrator $administrator)
    {
        $queryBuilder = $this->createQueryBuilder('connectionLogs')
            ->where('connectionLogs.administrator = :administrator')
            ->setParameter('administrator', $administrator)
            ->orderBy('connectionLogs.connectionDate', 'DESC');

        return $queryBuilder;
    }
}

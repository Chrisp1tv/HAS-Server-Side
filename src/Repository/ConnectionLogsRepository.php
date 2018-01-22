<?php

namespace App\Repository;

use App\Entity\Administrator;
use App\Entity\ConnectionLogs;
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
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ConnectionLogs::class);
    }

    public function findConnectionLogsByAdministrator(Administrator $administrator): array
    {
        $queryBuilder = $this->findByAdministrator($administrator);

        return $queryBuilder
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @param Administrator $administrator
     *
     * @return ConnectionLogs
     */
    public function findPenultimateConnectionByAdministrator(Administrator $administrator): ?ConnectionLogs
    {
        $queryBuilder = $this->findByAdministrator($administrator);

        return $queryBuilder
            ->getQuery()
            ->setFirstResult(1)
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }

    private function findByAdministrator(Administrator $administrator): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('connectionLogs')
            ->where('connectionLogs.administrator = :administrator')
            ->setParameter('administrator', $administrator)
            ->orderBy('connectionLogs.connectionDate', 'DESC');

        return $queryBuilder;
    }
}
<?php

namespace App\Repository;

use App\Entity\Administrator;
use App\Util\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * AdministratorRepository
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class AdministratorRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Administrator::class);
    }

    public function findAllPaginated($itemsPerPage, $page)
    {
        $queryBuilder = $this->createQueryBuilder('administrator');

        return new Paginator($queryBuilder->getQuery(), $itemsPerPage, $page);
    }
}
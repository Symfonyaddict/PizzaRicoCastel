<?php

namespace App\Repository;

use App\Entity\MentionsLegales;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MentionsLegales>
 */
class MentionsLegalesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MentionsLegales::class);
    }

    public function findPublished(): ?MentionsLegales
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.pageName = :pageName')
            ->setParameter('pageName', MentionsLegales::PAGE_NAME)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}

<?php

namespace App\Repository;

use App\Entity\Boisson;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Boisson>
 */
class BoissonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Boisson::class);
    }

    /**
     * @return Boisson[] Returns an array of Boisson objects ordered by price (large) in descending order
     */
    public function findAllOrderByPriceAsc(): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.price', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
    
    /**
     * @return Boisson[] Returns an array of Boisson objects that are special, ordered by price (large) in descending order
     */
    public function findSpecialOrderByPriceAsc(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.isSpecial = :special')
            ->setParameter('special', true)
            ->orderBy('p.price', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    //    public function findOneBySomeField($value): ?Pizza
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

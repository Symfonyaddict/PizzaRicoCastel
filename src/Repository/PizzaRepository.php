<?php

namespace App\Repository;

use App\Entity\Pizza;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pizza>
 */
class PizzaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pizza::class);
    }

    /**
     * @return Pizza[] Returns an array of Pizza objects ordered by price (large) in descending order
     */
    public function findAllOrderByPriceAsc(): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.priceLarge', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
    
    /**
     * @return Pizza[] Returns an array of Pizza objects that are special, ordered by price (large) in descending order
     */
    public function findSpecialOrderByPriceAsc(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.isSpecial = :special')
            ->setParameter('special', true)
            ->orderBy('p.priceLarge', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Retourne le prix moyen du format Large (calcul direct en DQL pour éviter findAll).
     */
    public function findAvgPriceLarge(): ?float
    {
        $result = $this->createQueryBuilder('p')
            ->select('AVG(p.priceLarge) as avgPriceLarge')
            ->getQuery()
            ->getSingleScalarResult();

        if (null === $result) {
            return null;
        }

        return (float) $result;
    }
}

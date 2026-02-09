<?php

namespace App\Repository;

use App\Entity\Cryptocurrency;
use App\Entity\Quote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Quote>
 */
class QuoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quote::class);
    }

    public function findByPeriod(Cryptocurrency $crypto, int $days = 30): array
    {
        $date = new \DateTimeImmutable("-{$days} days");

        return $this->createQueryBuilder('q')
            ->where('q.cryptocurrency = :crypto')
            ->andWhere('q.createdAt >= :date')
            ->setParameter('crypto', $crypto)
            ->setParameter('date', $date)
            ->orderBy('q.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findLatestByCrypto(Cryptocurrency $crypto): ?Quote
    {
        return $this->createQueryBuilder('q')
            ->where('q.cryptocurrency = :crypto')
            ->setParameter('crypto', $crypto)
            ->orderBy('q.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}

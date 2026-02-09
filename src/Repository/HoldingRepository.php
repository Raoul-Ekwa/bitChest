<?php

namespace App\Repository;

use App\Entity\Cryptocurrency;
use App\Entity\Holding;
use App\Entity\Wallet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Holding>
 */
class HoldingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Holding::class);
    }

    public function findByWalletAndCrypto(Wallet $wallet, Cryptocurrency $crypto): ?Holding
    {
        return $this->findOneBy([
            'wallet' => $wallet,
            'cryptocurrency' => $crypto
        ]);
    }

    public function findByWallet(Wallet $wallet): array
    {
        return $this->createQueryBuilder('h')
            ->where('h.wallet = :wallet')
            ->andWhere('h.quantity > 0')
            ->setParameter('wallet', $wallet)
            ->getQuery()
            ->getResult();
    }
}

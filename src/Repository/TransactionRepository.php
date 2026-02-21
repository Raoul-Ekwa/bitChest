<?php

namespace App\Repository;

use App\Entity\Transaction;
use App\Entity\Wallet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Transaction>
 */
class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    public function findByWallet(Wallet $wallet, int $limit = 50): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.wallet = :wallet')
            ->setParameter('wallet', $wallet)
            ->orderBy('t.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findRecentTransactions(int $limit = 10): array
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findBuyTransactionsByWalletGroupedByCrypto(Wallet $wallet): array
    {
        $transactions = $this->createQueryBuilder('t')
            ->where('t.wallet = :wallet')
            ->andWhere('t.type = :type')
            ->setParameter('wallet', $wallet)
            ->setParameter('type', 'buy')
            ->orderBy('t.createdAt', 'ASC')
            ->getQuery()
            ->getResult();

        $grouped = [];
        foreach ($transactions as $transaction) {
            $cryptoId = $transaction->getCryptocurrency()->getId();
            $grouped[$cryptoId][] = $transaction;
        }

        return $grouped;
    }
}

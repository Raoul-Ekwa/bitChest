<?php

namespace App\Repository;

use App\Entity\Client;
use App\Entity\Wallet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Wallet>
 */
class WalletRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Wallet::class);
    }

    public function findByClient(Client $client): ?Wallet
    {
        return $this->findOneBy(['client' => $client]);
    }
}

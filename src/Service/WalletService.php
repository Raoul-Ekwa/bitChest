<?php

namespace App\Service;

use App\Entity\Client;
use App\Entity\Cryptocurrency;
use App\Entity\Holding;
use App\Entity\Wallet;
use App\Repository\HoldingRepository;
use App\Repository\WalletRepository;
use Doctrine\ORM\EntityManagerInterface;

class WalletService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private WalletRepository $walletRepository,
        private HoldingRepository $holdingRepository,
        private CalculationService $calculationService
    ) {}

    public function createWalletForClient(Client $client, string $initialBalance = '500.00'): Wallet
    {
        $wallet = new Wallet();
        $wallet->setClient($client);
        $wallet->setBalance($initialBalance);

        $this->entityManager->persist($wallet);

        return $wallet;
    }

    public function getWalletByClient(Client $client): ?Wallet
    {
        return $client->getWallet();
    }

    public function getBalance(Wallet $wallet): string
    {
        return $wallet->getBalance();
    }

    public function getHolding(Wallet $wallet, Cryptocurrency $crypto): ?Holding
    {
        return $this->holdingRepository->findOneBy([
            'wallet' => $wallet,
            'cryptocurrency' => $crypto
        ]);
    }

    public function getOrCreateHolding(Wallet $wallet, Cryptocurrency $crypto): Holding
    {
        $holding = $this->getHolding($wallet, $crypto);

        if (!$holding) {
            $holding = new Holding();
            $holding->setWallet($wallet);
            $holding->setCryptocurrency($crypto);
            $this->entityManager->persist($holding);
        }

        return $holding;
    }

    public function hasEnoughBalance(Wallet $wallet, string $amount): bool
    {
        return bccomp($wallet->getBalance(), $amount, 2) >= 0;
    }

    public function hasEnoughCrypto(Wallet $wallet, Cryptocurrency $crypto, string $quantity): bool
    {
        $holding = $this->getHolding($wallet, $crypto);

        if (!$holding) {
            return false;
        }

        return bccomp($holding->getQuantity(), $quantity, 8) >= 0;
    }

    public function getPortfolioSummary(Wallet $wallet): array
    {
        return [
            'balance' => $wallet->getBalance(),
            'portfolioValue' => $this->calculationService->calculateTotalPortfolioValue($wallet),
            'portfolioCost' => $this->calculationService->calculateTotalPortfolioCost($wallet),
            'profitLoss' => $this->calculationService->calculateTotalProfitLoss($wallet),
            'profitLossPercentage' => $this->calculationService->calculateTotalProfitLossPercentage($wallet),
            'netWorth' => $this->calculationService->calculateNetWorth($wallet),
            'holdings' => $wallet->getHoldings()
        ];
    }

    public function getAllHoldings(Wallet $wallet): array
    {
        return $wallet->getHoldings()->toArray();
    }

    public function getTransactionHistory(Wallet $wallet, int $limit = 50): array
    {
        return $wallet->getTransactions()->slice(0, $limit);
    }
}

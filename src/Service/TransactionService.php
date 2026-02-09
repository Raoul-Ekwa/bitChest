<?php

namespace App\Service;

use App\Entity\Cryptocurrency;
use App\Entity\Transaction;
use App\Entity\Wallet;
use App\Repository\TransactionRepository;
use Doctrine\ORM\EntityManagerInterface;

class TransactionService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TransactionRepository $transactionRepository,
        private WalletService $walletService,
        private CalculationService $calculationService
    ) {}

    public function buy(Wallet $wallet, Cryptocurrency $crypto, string $quantity): Transaction
    {
        $price = $crypto->getCurrentPrice();
        $totalAmount = $this->calculationService->calculateTransactionTotal($quantity, $price);

        // Verify sufficient balance
        if (!$this->walletService->hasEnoughBalance($wallet, $totalAmount)) {
            throw new \InvalidArgumentException('Insufficient balance for this purchase.');
        }

        // Create transaction
        $transaction = new Transaction();
        $transaction->setWallet($wallet);
        $transaction->setCryptocurrency($crypto);
        $transaction->setType(Transaction::TYPE_BUY);
        $transaction->setQuantity($quantity);
        $transaction->setPriceAtTransaction($price);
        $transaction->setTotalAmount($totalAmount);

        // Update wallet balance
        $wallet->subtractFromBalance($totalAmount);
        $wallet->setUpdatedAt(new \DateTimeImmutable());

        // Update or create holding
        $holding = $this->walletService->getOrCreateHolding($wallet, $crypto);

        $newAvgPrice = $this->calculationService->calculateNewAveragePrice(
            $holding->getQuantity(),
            $holding->getAveragePurchasePrice(),
            $quantity,
            $price
        );

        $holding->addQuantity($quantity);
        $holding->setAveragePurchasePrice($newAvgPrice);
        $holding->setUpdatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($transaction);
        $this->entityManager->flush();

        return $transaction;
    }

    public function sell(Wallet $wallet, Cryptocurrency $crypto, string $quantity): Transaction
    {
        $price = $crypto->getCurrentPrice();
        $totalAmount = $this->calculationService->calculateTransactionTotal($quantity, $price);

        // Verify sufficient crypto
        if (!$this->walletService->hasEnoughCrypto($wallet, $crypto, $quantity)) {
            throw new \InvalidArgumentException('Insufficient quantity for this sale.');
        }

        // Create transaction
        $transaction = new Transaction();
        $transaction->setWallet($wallet);
        $transaction->setCryptocurrency($crypto);
        $transaction->setType(Transaction::TYPE_SELL);
        $transaction->setQuantity($quantity);
        $transaction->setPriceAtTransaction($price);
        $transaction->setTotalAmount($totalAmount);

        // Update wallet balance
        $wallet->addToBalance($totalAmount);
        $wallet->setUpdatedAt(new \DateTimeImmutable());

        // Update holding
        $holding = $this->walletService->getHolding($wallet, $crypto);
        $holding->subtractQuantity($quantity);
        $holding->setUpdatedAt(new \DateTimeImmutable());

        // Remove holding if quantity is zero
        if (bccomp($holding->getQuantity(), '0', 8) === 0) {
            $this->entityManager->remove($holding);
        }

        $this->entityManager->persist($transaction);
        $this->entityManager->flush();

        return $transaction;
    }

    public function getTransactionsByWallet(Wallet $wallet, int $limit = 50): array
    {
        return $this->transactionRepository->findBy(
            ['wallet' => $wallet],
            ['createdAt' => 'DESC'],
            $limit
        );
    }

    public function getTransactionsByCrypto(Wallet $wallet, Cryptocurrency $crypto): array
    {
        return $this->transactionRepository->findBy(
            ['wallet' => $wallet, 'cryptocurrency' => $crypto],
            ['createdAt' => 'DESC']
        );
    }
}

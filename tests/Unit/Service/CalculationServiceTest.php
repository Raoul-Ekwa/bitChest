<?php

namespace App\Tests\Unit\Service;

use App\Entity\Cryptocurrency;
use App\Entity\Holding;
use App\Entity\Wallet;
use App\Service\CalculationService;
use PHPUnit\Framework\TestCase;

class CalculationServiceTest extends TestCase
{
    private CalculationService $service;

    protected function setUp(): void
    {
        $this->service = new CalculationService();
    }

    private function makeCrypto(string $price): Cryptocurrency
    {
        $crypto = new Cryptocurrency();
        $crypto->setSymbol('BTC');
        $crypto->setName('Bitcoin');
        $crypto->setCurrentPrice($price);
        return $crypto;
    }

    private function makeHolding(Wallet $wallet, Cryptocurrency $crypto, string $quantity, string $avgPrice): Holding
    {
        $holding = new Holding();
        $holding->setWallet($wallet);
        $holding->setCryptocurrency($crypto);
        $holding->setQuantity($quantity);
        $holding->setAveragePurchasePrice($avgPrice);
        $wallet->addHolding($holding);
        return $holding;
    }

    // --- calculateTotalPortfolioValue ---

    public function testCalculateTotalPortfolioValueEmptyWallet(): void
    {
        $wallet = new Wallet();
        $this->assertSame('0.00', $this->service->calculateTotalPortfolioValue($wallet));
    }

    public function testCalculateTotalPortfolioValueWithHoldings(): void
    {
        $wallet = new Wallet();
        $crypto = $this->makeCrypto('30000.00');
        // 2 BTC at 30000 = 60000
        $this->makeHolding($wallet, $crypto, '2.00000000', '14500.00000000');

        $this->assertSame('60000.00', $this->service->calculateTotalPortfolioValue($wallet));
    }

    public function testCalculateTotalPortfolioValueSumsMultipleHoldings(): void
    {
        $wallet = new Wallet();
        $btc = $this->makeCrypto('30000.00');
        $eth = $this->makeCrypto('2000.00');
        $this->makeHolding($wallet, $btc, '1.00000000', '10000.00000000');
        $this->makeHolding($wallet, $eth, '5.00000000', '1500.00000000');

        // 30000 + 10000 = 40000
        $this->assertSame('40000.00', $this->service->calculateTotalPortfolioValue($wallet));
    }

    // --- calculateTotalProfitLoss ---

    public function testCalculateTotalProfitLossProfit(): void
    {
        $wallet = new Wallet();
        $crypto = $this->makeCrypto('30000.00');
        // cost: 2 * 14500 = 29000 — value: 2 * 30000 = 60000 — P&L = 31000
        $this->makeHolding($wallet, $crypto, '2.00000000', '14500.00000000');

        $this->assertSame('31000.00', $this->service->calculateTotalProfitLoss($wallet));
    }

    public function testCalculateTotalProfitLossLoss(): void
    {
        $wallet = new Wallet();
        $crypto = $this->makeCrypto('8000.00');
        // cost: 1 * 10000 = 10000 — value: 1 * 8000 = 8000 — P&L = -2000
        $this->makeHolding($wallet, $crypto, '1.00000000', '10000.00000000');

        $this->assertSame('-2000.00', $this->service->calculateTotalProfitLoss($wallet));
    }

    public function testCalculateTotalProfitLossEmptyWalletIsZero(): void
    {
        $wallet = new Wallet();
        $this->assertSame('0.00', $this->service->calculateTotalProfitLoss($wallet));
    }

    // --- calculateTotalProfitLossPercentage ---

    public function testCalculateProfitLossPercentageWithZeroCostReturnsZero(): void
    {
        $wallet = new Wallet();
        $this->assertSame('0.00', $this->service->calculateTotalProfitLossPercentage($wallet));
    }

    public function testCalculateProfitLossPercentage(): void
    {
        $wallet = new Wallet();
        $crypto = $this->makeCrypto('11000.00');
        // cost: 10000, value: 11000 → +10%
        $this->makeHolding($wallet, $crypto, '1.00000000', '10000.00000000');

        $this->assertSame('10.00', $this->service->calculateTotalProfitLossPercentage($wallet));
    }

    // --- calculateNetWorth ---

    public function testCalculateNetWorthEqualsBalancePlusPortfolioValue(): void
    {
        $wallet = new Wallet();
        $wallet->setBalance('500.00');
        $crypto = $this->makeCrypto('30000.00');
        $this->makeHolding($wallet, $crypto, '1.00000000', '25000.00000000');

        // 500 + 30000 = 30500
        $this->assertSame('30500.00', $this->service->calculateNetWorth($wallet));
    }

    public function testCalculateNetWorthEmptyWalletEqualsBalance(): void
    {
        $wallet = new Wallet();
        $wallet->setBalance('500.00');
        $this->assertSame('500.00', $this->service->calculateNetWorth($wallet));
    }

    // --- calculateNewAveragePrice (exemple du CDC : Bruno achète 3 fois du BTC) ---

    public function testCalculateNewAveragePriceFromCDCExample(): void
    {
        // Achat 1 : 1 BTC à 10 000 + Achat 2 : 0.5 BTC à 18 000 → avg = 12 666.666...
        $avg = $this->service->calculateNewAveragePrice('1.00000000', '10000.00000000', '0.50000000', '18000.00000000');

        // Achat 3 : 0.5 BTC à 20 000 sur la base de l'avg précédent
        $finalAvg = $this->service->calculateNewAveragePrice('1.50000000', $avg, '0.50000000', '20000.00000000');

        // Coût total : (1×10000) + (0.5×18000) + (0.5×20000) = 29000 / 2 BTC = 14500
        $this->assertSame('14500.00000000', $finalAvg);
    }

    public function testCalculateNewAveragePriceWithZeroInitialQuantity(): void
    {
        $avg = $this->service->calculateNewAveragePrice('0.00000000', '0.00000000', '1.00000000', '42500.00000000');
        $this->assertSame('42500.00000000', $avg);
    }

    // --- calculateTransactionTotal ---

    public function testCalculateTransactionTotal(): void
    {
        $total = $this->service->calculateTransactionTotal('0.50000000', '42500.00');
        $this->assertSame('21250.00', $total);
    }

    public function testCalculateTransactionTotalWithSmallQuantity(): void
    {
        $total = $this->service->calculateTransactionTotal('0.00100000', '50000.00');
        $this->assertSame('50.00', $total);
    }
}

<?php

namespace App\Tests\Unit\Service;

use App\Entity\Cryptocurrency;
use App\Entity\Holding;
use App\Entity\Wallet;
use App\Repository\HoldingRepository;
use App\Repository\WalletRepository;
use App\Service\CalculationService;
use App\Service\WalletService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class WalletServiceTest extends TestCase
{
    private WalletService $service;
    private HoldingRepository $holdingRepository;

    protected function setUp(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $walletRepository = $this->createMock(WalletRepository::class);
        $this->holdingRepository = $this->createMock(HoldingRepository::class);
        $calculationService = new CalculationService();

        $this->service = new WalletService(
            $entityManager,
            $walletRepository,
            $this->holdingRepository,
            $calculationService
        );
    }

    private function makeCrypto(string $price = '42500.00'): Cryptocurrency
    {
        $crypto = new Cryptocurrency();
        $crypto->setSymbol('BTC');
        $crypto->setName('Bitcoin');
        $crypto->setCurrentPrice($price);
        return $crypto;
    }

    // --- hasEnoughBalance ---

    public function testHasEnoughBalanceReturnsTrueWhenSufficientFunds(): void
    {
        $wallet = new Wallet();
        $wallet->setBalance('500.00');

        $this->assertTrue($this->service->hasEnoughBalance($wallet, '500.00'));
    }

    public function testHasEnoughBalanceReturnsTrueWhenMoreThanEnough(): void
    {
        $wallet = new Wallet();
        $wallet->setBalance('1000.00');

        $this->assertTrue($this->service->hasEnoughBalance($wallet, '500.00'));
    }

    public function testHasEnoughBalanceReturnsFalseWhenInsufficientFunds(): void
    {
        $wallet = new Wallet();
        $wallet->setBalance('100.00');

        $this->assertFalse($this->service->hasEnoughBalance($wallet, '500.00'));
    }

    public function testHasEnoughBalanceReturnsFalseWhenBalanceIsZero(): void
    {
        $wallet = new Wallet();
        $wallet->setBalance('0.00');

        $this->assertFalse($this->service->hasEnoughBalance($wallet, '0.01'));
    }

    // --- hasEnoughCrypto ---

    public function testHasEnoughCryptoReturnsFalseWhenNoHolding(): void
    {
        $wallet = new Wallet();
        $crypto = $this->makeCrypto();

        $this->holdingRepository
            ->method('findOneBy')
            ->willReturn(null);

        $this->assertFalse($this->service->hasEnoughCrypto($wallet, $crypto, '0.50000000'));
    }

    public function testHasEnoughCryptoReturnsTrueWhenSufficientQuantity(): void
    {
        $wallet = new Wallet();
        $crypto = $this->makeCrypto();

        $holding = new Holding();
        $holding->setQuantity('2.00000000');

        $this->holdingRepository
            ->method('findOneBy')
            ->willReturn($holding);

        $this->assertTrue($this->service->hasEnoughCrypto($wallet, $crypto, '1.00000000'));
    }

    public function testHasEnoughCryptoReturnsFalseWhenInsufficientQuantity(): void
    {
        $wallet = new Wallet();
        $crypto = $this->makeCrypto();

        $holding = new Holding();
        $holding->setQuantity('0.10000000');

        $this->holdingRepository
            ->method('findOneBy')
            ->willReturn($holding);

        $this->assertFalse($this->service->hasEnoughCrypto($wallet, $crypto, '1.00000000'));
    }

    // --- getPortfolioSummary ---

    public function testGetPortfolioSummaryReturnsAllExpectedKeys(): void
    {
        $wallet = new Wallet();
        $wallet->setBalance('500.00');

        $summary = $this->service->getPortfolioSummary($wallet);

        $this->assertArrayHasKey('balance', $summary);
        $this->assertArrayHasKey('portfolioValue', $summary);
        $this->assertArrayHasKey('portfolioCost', $summary);
        $this->assertArrayHasKey('profitLoss', $summary);
        $this->assertArrayHasKey('profitLossPercentage', $summary);
        $this->assertArrayHasKey('netWorth', $summary);
        $this->assertArrayHasKey('holdings', $summary);
    }

    public function testGetPortfolioSummaryWithEmptyWallet(): void
    {
        $wallet = new Wallet();
        $wallet->setBalance('500.00');

        $summary = $this->service->getPortfolioSummary($wallet);

        $this->assertSame('500.00', $summary['balance']);
        $this->assertSame('0.00', $summary['portfolioValue']);
        $this->assertSame('0.00', $summary['profitLoss']);
        $this->assertSame('500.00', $summary['netWorth']);
    }
}

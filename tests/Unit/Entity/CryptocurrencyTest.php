<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Cryptocurrency;
use App\Entity\Quote;
use PHPUnit\Framework\TestCase;

class CryptocurrencyTest extends TestCase
{
    // --- symbol ---

    public function testSetSymbolForcesUppercase(): void
    {
        $crypto = new Cryptocurrency();
        $crypto->setSymbol('btc');
        $this->assertSame('BTC', $crypto->getSymbol());
    }

    public function testSetSymbolMixedCaseIsUppercased(): void
    {
        $crypto = new Cryptocurrency();
        $crypto->setSymbol('eThErEuM');
        $this->assertSame('ETHEREUM', $crypto->getSymbol());
    }

    // --- construction defaults ---

    public function testCreatedAtIsSetOnConstruction(): void
    {
        $crypto = new Cryptocurrency();
        $this->assertInstanceOf(\DateTimeImmutable::class, $crypto->getCreatedAt());
    }

    public function testDefaultCurrentPriceIsZero(): void
    {
        $crypto = new Cryptocurrency();
        $this->assertSame('0.00000000', $crypto->getCurrentPrice());
    }

    public function testImageIsNullByDefault(): void
    {
        $crypto = new Cryptocurrency();
        $this->assertNull($crypto->getImage());
    }

    public function testCollectionsAreEmptyOnConstruction(): void
    {
        $crypto = new Cryptocurrency();
        $this->assertCount(0, $crypto->getQuotes());
        $this->assertCount(0, $crypto->getHoldings());
        $this->assertCount(0, $crypto->getTransactions());
    }

    // --- setters / getters ---

    public function testSetAndGetName(): void
    {
        $crypto = new Cryptocurrency();
        $crypto->setName('Bitcoin');
        $this->assertSame('Bitcoin', $crypto->getName());
    }

    public function testSetAndGetCurrentPrice(): void
    {
        $crypto = new Cryptocurrency();
        $crypto->setCurrentPrice('42500.12345678');
        $this->assertSame('42500.12345678', $crypto->getCurrentPrice());
    }

    public function testSetAndGetImage(): void
    {
        $crypto = new Cryptocurrency();
        $crypto->setImage('https://example.com/btc.png');
        $this->assertSame('https://example.com/btc.png', $crypto->getImage());
    }

    public function testSetImageToNull(): void
    {
        $crypto = new Cryptocurrency();
        $crypto->setImage('https://example.com/btc.png');
        $crypto->setImage(null);
        $this->assertNull($crypto->getImage());
    }

    // --- quotes ---

    public function testGetLatestQuoteReturnsNullWhenNoQuotes(): void
    {
        $crypto = new Cryptocurrency();
        $this->assertNull($crypto->getLatestQuote());
    }

    public function testGetLatestQuoteReturnsAddedQuote(): void
    {
        $crypto = new Cryptocurrency();
        $quote = new Quote();
        $quote->setPrice('50000.00000000');
        $crypto->addQuote($quote);

        $this->assertSame($quote, $crypto->getLatestQuote());
    }

    public function testAddQuoteSetsBackReference(): void
    {
        $crypto = new Cryptocurrency();
        $quote = new Quote();
        $quote->setPrice('1000.00000000');
        $crypto->addQuote($quote);

        $this->assertSame($crypto, $quote->getCryptocurrency());
    }

    public function testAddQuoteDoesNotDuplicate(): void
    {
        $crypto = new Cryptocurrency();
        $quote = new Quote();
        $quote->setPrice('1000.00000000');
        $crypto->addQuote($quote);
        $crypto->addQuote($quote);

        $this->assertCount(1, $crypto->getQuotes());
    }

    public function testRemoveQuote(): void
    {
        $crypto = new Cryptocurrency();
        $quote = new Quote();
        $quote->setPrice('1000.00000000');
        $crypto->addQuote($quote);
        $crypto->removeQuote($quote);

        $this->assertCount(0, $crypto->getQuotes());
        $this->assertNull($quote->getCryptocurrency());
    }
}

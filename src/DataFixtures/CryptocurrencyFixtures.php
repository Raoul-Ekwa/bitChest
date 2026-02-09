<?php

namespace App\DataFixtures;

use App\Entity\Cryptocurrency;
use App\Entity\Quote;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CryptocurrencyFixtures extends Fixture
{
    public const CRYPTO_REFERENCE_PREFIX = 'crypto_';

    private array $cryptocurrencies = [
        ['symbol' => 'BTC', 'name' => 'Bitcoin', 'price' => '42500.00'],
        ['symbol' => 'ETH', 'name' => 'Ethereum', 'price' => '2280.00'],
        ['symbol' => 'XRP', 'name' => 'Ripple', 'price' => '0.52'],
        ['symbol' => 'BCH', 'name' => 'Bitcoin Cash', 'price' => '235.00'],
        ['symbol' => 'ADA', 'name' => 'Cardano', 'price' => '0.48'],
        ['symbol' => 'LTC', 'name' => 'Litecoin', 'price' => '68.50'],
        ['symbol' => 'DASH', 'name' => 'Dash', 'price' => '27.50'],
        ['symbol' => 'IOTA', 'name' => 'IOTA', 'price' => '0.18'],
        ['symbol' => 'XEM', 'name' => 'NEM', 'price' => '0.025'],
        ['symbol' => 'XLM', 'name' => 'Stellar', 'price' => '0.11'],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach ($this->cryptocurrencies as $data) {
            $crypto = new Cryptocurrency();
            $crypto->setSymbol($data['symbol']);
            $crypto->setName($data['name']);
            $crypto->setCurrentPrice($data['price']);
            $crypto->setImage('/images/crypto/' . strtolower($data['symbol']) . '.png');

            $manager->persist($crypto);

            // Generate historical quotes for the last 30 days
            $this->generateHistoricalQuotes($manager, $crypto, $data['price']);

            $this->addReference(self::CRYPTO_REFERENCE_PREFIX . $data['symbol'], $crypto);
        }

        $manager->flush();
    }

    private function generateHistoricalQuotes(ObjectManager $manager, Cryptocurrency $crypto, string $currentPrice): void
    {
        $price = (float) $currentPrice;

        // Generate quotes for the last 30 days
        for ($day = 30; $day >= 0; $day--) {
            $date = new \DateTimeImmutable("-{$day} days");

            // Simulate price variation (+/- 5% per day)
            $variation = (mt_rand(-500, 500) / 10000);
            $dailyPrice = $price * (1 + $variation);

            $quote = new Quote();
            $quote->setCryptocurrency($crypto);
            $quote->setPrice(number_format($dailyPrice, 8, '.', ''));
            $quote->setCreatedAt($date);

            $manager->persist($quote);

            // Update price for next iteration to create realistic trend
            $price = $dailyPrice;
        }

        // Set the final price as current price
        $crypto->setCurrentPrice(number_format($price, 8, '.', ''));
    }
}

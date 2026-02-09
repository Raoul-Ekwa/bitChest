<?php

namespace App\Service;

use App\Entity\Cryptocurrency;
use App\Entity\Quote;
use App\Repository\CryptocurrencyRepository;
use App\Repository\QuoteRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Service for generating cryptocurrency quotations.
 * Based on the cotation_generator.php specification.
 */
class QuoteGeneratorService
{
    // Base prices for the first quotation of each cryptocurrency
    private const FIRST_COTATIONS = [
        'BTC' => 35000.00,
        'ETH' => 2000.00,
        'XRP' => 0.50,
        'BCH' => 200.00,
        'ADA' => 0.40,
        'LTC' => 60.00,
        'DASH' => 25.00,
        'IOTA' => 0.15,
        'XEM' => 0.02,
        'XLM' => 0.10,
    ];

    public function __construct(
        private EntityManagerInterface $entityManager,
        private CryptocurrencyRepository $cryptocurrencyRepository,
        private QuoteRepository $quoteRepository
    ) {}

    /**
     * Get the first quotation for a cryptocurrency.
     * This is the base price used to start generating historical data.
     */
    public function getFirstCotation(string $symbol): float
    {
        return self::FIRST_COTATIONS[$symbol] ?? 100.00;
    }

    /**
     * Generate the next quotation based on the previous price.
     * Uses a random variation algorithm to simulate market fluctuations.
     * Price cannot be negative.
     */
    public function generateCotation(float $previousPrice): float
    {
        // Variation between -5% and +5%
        $variation = (mt_rand(-500, 500) / 10000);

        // Calculate new price
        $newPrice = $previousPrice * (1 + $variation);

        // Ensure price is never negative (minimum 0.00000001)
        return max($newPrice, 0.00000001);
    }

    /**
     * Generate quotations for a specific cryptocurrency for the last N days.
     */
    public function generateQuotesForCrypto(Cryptocurrency $crypto, int $days = 30): void
    {
        // Get the first cotation as starting point
        $price = $this->getFirstCotation($crypto->getSymbol());

        // Generate quotes for each day
        for ($day = $days; $day >= 0; $day--) {
            $date = new \DateTimeImmutable("-{$day} days");

            // Generate the daily price based on previous price
            $price = $this->generateCotation($price);

            // Create and persist the quote
            $quote = new Quote();
            $quote->setCryptocurrency($crypto);
            $quote->setPrice(number_format($price, 8, '.', ''));
            $quote->setCreatedAt($date);

            $this->entityManager->persist($quote);
        }

        // Update the cryptocurrency's current price to the latest quote
        $crypto->setCurrentPrice(number_format($price, 8, '.', ''));
    }

    /**
     * Generate today's quotation for all cryptocurrencies.
     * This should be run daily via a cron job or Symfony command.
     */
    public function generateDailyQuotes(): int
    {
        $count = 0;
        $cryptocurrencies = $this->cryptocurrencyRepository->findAll();
        $today = new \DateTimeImmutable('today');

        foreach ($cryptocurrencies as $crypto) {
            // Get the latest quote for this cryptocurrency
            $latestQuote = $this->quoteRepository->findLatestByCrypto($crypto);

            if ($latestQuote) {
                // Check if we already have a quote for today
                if ($latestQuote->getCreatedAt()->format('Y-m-d') === $today->format('Y-m-d')) {
                    continue;
                }

                $previousPrice = (float) $latestQuote->getPrice();
            } else {
                $previousPrice = $this->getFirstCotation($crypto->getSymbol());
            }

            // Generate new price
            $newPrice = $this->generateCotation($previousPrice);

            // Create new quote
            $quote = new Quote();
            $quote->setCryptocurrency($crypto);
            $quote->setPrice(number_format($newPrice, 8, '.', ''));
            $quote->setCreatedAt($today);

            // Update current price
            $crypto->setCurrentPrice(number_format($newPrice, 8, '.', ''));

            $this->entityManager->persist($quote);
            $count++;
        }

        $this->entityManager->flush();

        return $count;
    }

    /**
     * Generate initial quotes for all cryptocurrencies (30 days of history).
     */
    public function generateInitialQuotes(int $days = 30): void
    {
        $cryptocurrencies = $this->cryptocurrencyRepository->findAll();

        foreach ($cryptocurrencies as $crypto) {
            $this->generateQuotesForCrypto($crypto, $days);
        }

        $this->entityManager->flush();
    }
}

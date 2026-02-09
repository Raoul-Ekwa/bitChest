<?php

namespace App\Service;

use App\Entity\Cryptocurrency;
use App\Repository\CryptocurrencyRepository;
use App\Repository\QuoteRepository;

class CryptocurrencyService
{
    public function __construct(
        private CryptocurrencyRepository $cryptoRepository,
        private QuoteRepository $quoteRepository
    ) {}

    public function getAllCryptocurrencies(): array
    {
        return $this->cryptoRepository->findAll();
    }

    public function getCryptocurrency(int $id): ?Cryptocurrency
    {
        return $this->cryptoRepository->find($id);
    }

    public function getCryptocurrencyBySymbol(string $symbol): ?Cryptocurrency
    {
        return $this->cryptoRepository->findOneBy(['symbol' => strtoupper($symbol)]);
    }

    public function getPriceHistory(Cryptocurrency $crypto, int $days = 30): array
    {
        return $this->quoteRepository->findByPeriod($crypto, $days);
    }

    public function getCurrentPrice(Cryptocurrency $crypto): string
    {
        return $crypto->getCurrentPrice();
    }

    public function getPriceChange24h(Cryptocurrency $crypto): array
    {
        $quotes = $this->quoteRepository->findByPeriod($crypto, 1);

        if (count($quotes) < 2) {
            return ['amount' => '0.00', 'percentage' => '0.00'];
        }

        $currentPrice = $crypto->getCurrentPrice();
        $oldPrice = $quotes[count($quotes) - 1]->getPrice();

        $change = bcsub($currentPrice, $oldPrice, 8);
        $percentage = '0.00';

        if (bccomp($oldPrice, '0', 8) !== 0) {
            $percentage = bcmul(bcdiv($change, $oldPrice, 4), '100', 2);
        }

        return [
            'amount' => $change,
            'percentage' => $percentage
        ];
    }
}

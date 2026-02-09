<?php

namespace App\Service;

use App\Entity\Holding;
use App\Entity\Wallet;

class CalculationService
{
    public function calculateTotalPortfolioValue(Wallet $wallet): string
    {
        $total = '0.00';

        foreach ($wallet->getHoldings() as $holding) {
            $total = bcadd($total, $holding->getTotalValue(), 2);
        }

        return $total;
    }

    public function calculateTotalPortfolioCost(Wallet $wallet): string
    {
        $total = '0.00';

        foreach ($wallet->getHoldings() as $holding) {
            $total = bcadd($total, $holding->getTotalCost(), 2);
        }

        return $total;
    }

    public function calculateTotalProfitLoss(Wallet $wallet): string
    {
        $value = $this->calculateTotalPortfolioValue($wallet);
        $cost = $this->calculateTotalPortfolioCost($wallet);

        return bcsub($value, $cost, 2);
    }

    public function calculateTotalProfitLossPercentage(Wallet $wallet): string
    {
        $cost = $this->calculateTotalPortfolioCost($wallet);

        if (bccomp($cost, '0', 2) === 0) {
            return '0.00';
        }

        $profitLoss = $this->calculateTotalProfitLoss($wallet);
        return bcmul(bcdiv($profitLoss, $cost, 4), '100', 2);
    }

    public function calculateNetWorth(Wallet $wallet): string
    {
        $portfolioValue = $this->calculateTotalPortfolioValue($wallet);
        return bcadd($wallet->getBalance(), $portfolioValue, 2);
    }

    public function calculateNewAveragePrice(
        string $currentQuantity,
        string $currentAvgPrice,
        string $newQuantity,
        string $newPrice
    ): string {
        $totalQuantity = bcadd($currentQuantity, $newQuantity, 8);

        if (bccomp($totalQuantity, '0', 8) === 0) {
            return '0.00000000';
        }

        $currentTotal = bcmul($currentQuantity, $currentAvgPrice, 8);
        $newTotal = bcmul($newQuantity, $newPrice, 8);
        $combinedTotal = bcadd($currentTotal, $newTotal, 8);

        return bcdiv($combinedTotal, $totalQuantity, 8);
    }

    public function calculateTransactionTotal(string $quantity, string $price): string
    {
        return bcmul($quantity, $price, 2);
    }
}

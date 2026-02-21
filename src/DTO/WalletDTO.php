<?php

namespace App\DTO;

class WalletDTO
{
    public function __construct(
        public readonly string $balance = '0.00',
        public readonly string $portfolioValue = '0.00',
        public readonly string $profitLoss = '0.00',
        public readonly string $netWorth = '0.00',
    ) {}
}

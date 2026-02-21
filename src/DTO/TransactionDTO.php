<?php

namespace App\DTO;

class TransactionDTO
{
    public function __construct(
        public readonly string $type = '',
        public readonly string $quantity = '0.00000000',
        public readonly string $priceAtTransaction = '0.00',
        public readonly string $totalAmount = '0.00',
    ) {}
}

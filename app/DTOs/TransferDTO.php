<?php

namespace App\DTOs;

class TransferDTO
{
    /**
     * Create a new Transfer Data Transfer Object.
     * * Using PHP 8 constructor property promotion for clean DTOs.
     */
    public function __construct(
        public readonly int $sourceWalletId,
        public readonly int $targetWalletId,
        public readonly int $amount,
        public readonly ?string $idempotencyKey = null
    ) {}
}
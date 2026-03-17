<?php

namespace App\Services;

use App\Exceptions\InsufficientBalanceException;
use App\Models\Transaction;
use App\Repositories\TransactionRepository;
use App\Repositories\WalletRepository;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class WalletService
{
    public function __construct(
        protected WalletRepository $walletRepository,
        protected TransactionRepository $transactionRepository
    ) {}

    
    public function deposit(int $walletId, int $amount, ?string $idempotencyKey = null): Transaction
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Deposit amount must be greater than zero.');
        }

        // Idempotency check: If we've already processed this request
        if ($idempotencyKey) {
            $existingTransaction = $this->transactionRepository->findByIdempotencyKey($idempotencyKey);
            if ($existingTransaction) {
                return $existingTransaction;
            }
        }

        // DB::transaction ensures that if anything fails inside this block, 
        // all database changes are rolled back automatically (Atomicity).
        return DB::transaction(function () use ($walletId, $amount, $idempotencyKey) {
            // 1. Lock the wallet row to prevent race conditions
            $wallet = $this->walletRepository->findByIdLockForUpdate($walletId);

            if (!$wallet) {
                throw new InvalidArgumentException('Wallet not found.');
            }

            // 2. Update balance
            $this->walletRepository->updateBalance($wallet, $amount);

            // 3. Record transaction
            return $this->transactionRepository->create([
                'wallet_id' => $wallet->id,
                'type' => 'deposit',
                'amount' => $amount,
                'idempotency_key' => $idempotencyKey,
            ]);
        });
    }

   
    public function withdraw(int $walletId, int $amount, ?string $idempotencyKey = null): Transaction
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Withdrawal amount must be greater than zero.');
        }

        if ($idempotencyKey) {
            $existingTransaction = $this->transactionRepository->findByIdempotencyKey($idempotencyKey);
            if ($existingTransaction) {
                return $existingTransaction;
            }
        }

        return DB::transaction(function () use ($walletId, $amount, $idempotencyKey) {
            $wallet = $this->walletRepository->findByIdLockForUpdate($walletId);

            if (!$wallet) {
                throw new InvalidArgumentException('Wallet not found.');
            }

          
            if ($wallet->balance < $amount) {
                throw new InsufficientBalanceException("Wallet only has {$wallet->balance} available.");
            }

            // Subtract balance (passing negative amount)
            $this->walletRepository->updateBalance($wallet, -$amount);

            // Record transaction
            return $this->transactionRepository->create([
                'wallet_id' => $wallet->id,
                'type' => 'withdraw',
                'amount' => $amount,
                'idempotency_key' => $idempotencyKey,
            ]);
        });
    }
}
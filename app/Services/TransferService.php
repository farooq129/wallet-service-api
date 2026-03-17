<?php

namespace App\Services;

use App\DTOs\TransferDTO;
use App\Exceptions\InsufficientBalanceException;
use App\Repositories\TransactionRepository;
use App\Repositories\WalletRepository;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class TransferService
{
    public function __construct(
        protected WalletRepository $walletRepository,
        protected TransactionRepository $transactionRepository
    ) {}

   
    public function transfer(TransferDTO $dto): array
    {
        if ($dto->amount <= 0) {
            throw new InvalidArgumentException('Transfer amount must be greater than zero.');
        }

        if ($dto->sourceWalletId === $dto->targetWalletId) {
            throw new InvalidArgumentException('Self-transfers are not allowed.');
        }

        if ($dto->idempotencyKey) {
            $existingTransaction = $this->transactionRepository->findByIdempotencyKey($dto->idempotencyKey);
            if ($existingTransaction) {
               
                $relatedTransaction = $this->transactionRepository->findByIdempotencyKey($dto->idempotencyKey . '_target');
                return [
                    'source_transaction' => $existingTransaction,
                    'target_transaction' => $relatedTransaction
                ];
            }
        }

        return DB::transaction(function () use ($dto) {
            // Prevent deadlocks by always locking the lowest ID first
            $firstId = min($dto->sourceWalletId, $dto->targetWalletId);
            $secondId = max($dto->sourceWalletId, $dto->targetWalletId);

            $this->walletRepository->findByIdLockForUpdate($firstId);
            $this->walletRepository->findByIdLockForUpdate($secondId);

            // Fetch wallets after locks are acquired
            $sourceWallet = $this->walletRepository->findById($dto->sourceWalletId);
            $targetWallet = $this->walletRepository->findById($dto->targetWalletId);

            if (!$sourceWallet || !$targetWallet) {
                throw new InvalidArgumentException('One or both wallets not found.');
            }

            // Check Same currency 
            if ($sourceWallet->currency !== $targetWallet->currency) {
                throw new InvalidArgumentException('Transfers are only allowed between same-currency wallets.');
            }

            // Check Balance 
            if ($sourceWallet->balance < $dto->amount) {
                throw new InsufficientBalanceException("Source wallet has insufficient funds.");
            }

            // 1. Move the money
            $this->walletRepository->updateBalance($sourceWallet, -$dto->amount);
            $this->walletRepository->updateBalance($targetWallet, $dto->amount);

            // 2. Record transactions
            $sourceTx = $this->transactionRepository->create([
                'wallet_id' => $sourceWallet->id,
                'type' => 'transfer_out',
                'amount' => $dto->amount,
                'related_wallet_id' => $targetWallet->id,
                'idempotency_key' => $dto->idempotencyKey,
            ]);

           
            $targetTx = $this->transactionRepository->create([
                'wallet_id' => $targetWallet->id,
                'type' => 'transfer_in',
                'amount' => $dto->amount,
                'related_wallet_id' => $sourceWallet->id,
                'idempotency_key' => $dto->idempotencyKey ? $dto->idempotencyKey . '_target' : null, 
            ]);

            return [
                'source_transaction' => $sourceTx,
                'target_transaction' => $targetTx
            ];
        });
    }
}
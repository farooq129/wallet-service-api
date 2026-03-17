<?php

namespace App\Repositories;

use App\Models\Transaction;
use Illuminate\Pagination\LengthAwarePaginator;

class TransactionRepository
{
  
    public function create(array $data): Transaction
    {
        return Transaction::create($data);
    }

    // transaction by its idempotency key and prevent duplicate processing.
    public function findByIdempotencyKey(?string $key): ?Transaction
    {
        if (!$key) {
            return null;
        }

        return Transaction::where('idempotency_key', $key)->first();
    }

   
    public function getHistoryForWallet(int $walletId, array $filters = []): LengthAwarePaginator
    {
        // Get transactions where the wallet is either the source or the target
        $query = Transaction::where(function ($q) use ($walletId) {
            $q->where('wallet_id', $walletId)
              ->orWhere('related_wallet_id', $walletId);
        });

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween('created_at', [$filters['start_date'], $filters['end_date']]);
        }

        // Return chronological list, paginated
        return $query->orderBy('created_at', 'desc')->paginate(15);
    }
}
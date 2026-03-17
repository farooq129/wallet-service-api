<?php

namespace App\Repositories;

use App\Models\Wallet;
use Illuminate\Database\Eloquent\Collection;

class WalletRepository
{
    /**
     * Create a new wallet in the database.
     */
    public function create(array $data): Wallet
    {
        return Wallet::create($data);
    }

    /**
     * Find a wallet by its ID.
     */
    public function findById(int $id): ?Wallet
    {
        return Wallet::find($id);
    }

    /**
     * Find a wallet by ID and lock the row for update.
     * THIS IS CRUCIAL FOR FINANCIAL APPS: It prevents race conditions
     * if two requests try to update the balance at the exact same millisecond.
     */
    public function findByIdLockForUpdate(int $id): ?Wallet
    {
        return Wallet::where('id', $id)->lockForUpdate()->first();
    }

    /**
     * Update the balance of a wallet.
     */
    public function updateBalance(Wallet $wallet, int $amount): bool
    {
        $wallet->balance += $amount;
        return $wallet->save();
    }

    /**
     * Get all wallets with optional filtering.
     */
    public function getAll(array $filters = []): Collection
    {
        $query = Wallet::query();

        if (!empty($filters['owner_name'])) {
            $query->where('owner_name', 'like', '%' . $filters['owner_name'] . '%');
        }

        if (!empty($filters['currency'])) {
            $query->where('currency', strtoupper($filters['currency']));
        }

        return $query->get();
    }
}
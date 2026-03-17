<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\TransactionController;

// Health Check
Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});

// Wallet Management & Operations
Route::prefix('wallets')->group(function () {
    Route::post('/', [WalletController::class, 'store']);
    Route::get('/', [WalletController::class, 'index']);
    Route::get('/{id}', [WalletController::class, 'show']);
    Route::get('/{id}/balance', [WalletController::class, 'balance']);
    
    // Transactions inside a specific wallet
    Route::post('/{id}/deposit', [WalletController::class, 'deposit']);
    Route::post('/{id}/withdraw', [WalletController::class, 'withdraw']);
    Route::get('/{id}/transactions', [TransactionController::class, 'index']);
});

// Transfers between wallets
Route::post('/transfers', [TransferController::class, 'store']);
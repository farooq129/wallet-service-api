<?php

namespace App\Http\Controllers;

use App\Repositories\TransactionRepository;
use App\Repositories\WalletRepository;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function __construct(
        protected TransactionRepository $transactionRepository,
        protected WalletRepository $walletRepository
    ) {}

  
    public function index(Request $request, $id)
    {
        // First verify the wallet actually exists
        $wallet = $this->walletRepository->findById($id);
        
        if (!$wallet) {
            return response()->json(['error' => 'Wallet not found'], 404);
        }

        $filters = $request->only(['type', 'start_date', 'end_date']);
        $transactions = $this->transactionRepository->getHistoryForWallet($id, $filters);

        return response()->json($transactions);
    }
}
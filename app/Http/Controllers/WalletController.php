<?php

namespace App\Http\Controllers;

use App\Repositories\WalletRepository;
use App\Services\WalletService;
use App\Exceptions\InsufficientBalanceException;
use Illuminate\Http\Request;
use InvalidArgumentException;

class WalletController extends Controller
{
    public function __construct(
        protected WalletRepository $walletRepository,
        protected WalletService $walletService
    ) {}

    // POST /api/wallets
    public function store(Request $request)
    {
        $validated = $request->validate([
            'owner_name' => 'required|string|max:255',
            'national_id' => 'required|string|unique:wallets,national_id',
            'currency' => 'required|string|size:3',
        ]);

        $wallet = $this->walletRepository->create($validated);

        return response()->json($wallet, 201);
    }

    // GET /api/wallets/{id}
    public function show($id)
    {
        $wallet = $this->walletRepository->findById($id);

        if (!$wallet) {
            return response()->json(['error' => 'Wallet not found'], 404);
        }

        return response()->json($wallet);
    }

    // GET /api/wallets
    public function index(Request $request)
    {
        $filters = $request->only(['owner_name', 'currency']);
        $wallets = $this->walletRepository->getAll($filters);

        return response()->json($wallets);
    }

    // GET /api/wallets/{id}/balance
    public function balance($id)
    {
        $wallet = $this->walletRepository->findById($id);

        if (!$wallet) {
            return response()->json(['error' => 'Wallet not found'], 404);
        }

        return response()->json([
            'wallet_id' => $wallet->id,
            'balance' => $wallet->balance,
            'currency' => $wallet->currency
        ]);
    }

    // POST /api/wallets/{id}/deposit
    public function deposit(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|integer|min:1'
        ]);

        try {
            $transaction = $this->walletService->deposit(
                $id, 
                $request->amount, 
                $request->header('Idempotency-Key')
            );
            return response()->json($transaction, 201);
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    // POST /api/wallets/{id}/withdraw
    public function withdraw(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|integer|min:1'
        ]);

        try {
            $transaction = $this->walletService->withdraw(
                $id, 
                $request->amount, 
                $request->header('Idempotency-Key')
            );
            return response()->json($transaction, 201);
        } catch (InvalidArgumentException | InsufficientBalanceException $e) {
            
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
<?php

namespace App\Http\Controllers;

use App\DTOs\TransferDTO;
use App\Services\TransferService;
use App\Exceptions\InsufficientBalanceException;
use Illuminate\Http\Request;
use InvalidArgumentException;

class TransferController extends Controller
{
    public function __construct(
        protected TransferService $transferService
    ) {}

   
    public function store(Request $request)
    {
        $request->validate([
            'source_wallet_id' => 'required|integer|exists:wallets,id',
            'target_wallet_id' => 'required|integer|exists:wallets,id|different:source_wallet_id',
            'amount' => 'required|integer|min:1',
        ]);

        $dto = new TransferDTO(
            sourceWalletId: $request->source_wallet_id,
            targetWalletId: $request->target_wallet_id,
            amount: $request->amount,
            idempotencyKey: $request->header('Idempotency-Key')
        );

        try {
            $result = $this->transferService->transfer($dto);
            return response()->json($result, 201);
        } catch (InvalidArgumentException | InsufficientBalanceException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id',
        'type',
        'amount',
        'related_wallet_id',
        'idempotency_key'
    ];

   
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    
    public function relatedWallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'related_wallet_id');
    }
}
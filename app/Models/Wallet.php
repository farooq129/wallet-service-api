<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_name',
        'national_id',
        'currency',
        'balance'
    ];

    // Define the relationship: A wallet has many transactions
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
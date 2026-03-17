<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->cascadeOnDelete();
            
            // deposit, withdraw, transfer_in, transfer_out
            $table->string('type'); 
            
            // Always positive integer in minor units (e.g., 100 for $1.00)
            $table->bigInteger('amount'); 
            
            // For transfers, linking the other wallet involved
            $table->foreignId('related_wallet_id')->nullable()->constrained('wallets')->nullOnDelete();
            
            // To ensure idempotency (preventing duplicate processing of the same request)
            $table->string('idempotency_key')->nullable()->unique();
            
            $table->timestamps(); // Automatically adds created_at and updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
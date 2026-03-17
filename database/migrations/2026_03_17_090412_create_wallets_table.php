<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->string('owner_name');
            $table->string('national_id')->unique();
            $table->string('currency', 3); // e.g., USD, EUR
            $table->bigInteger('balance')->default(0); // Stored in minor units (e.g., cents)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
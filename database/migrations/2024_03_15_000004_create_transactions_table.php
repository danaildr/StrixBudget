<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_account_id')->constrained()->onDelete('cascade');
            $table->foreignId('transaction_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('counterparty_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('amount', 15, 2);
            $table->string('description')->nullable();
            $table->date('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
}; 
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
        Schema::create('recurring_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('bank_account_id')->constrained()->onDelete('cascade');
            $table->foreignId('counterparty_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('transaction_type_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('BGN');
            $table->string('description')->nullable();
            $table->string('repeat_type'); // daily, weekly, monthly, yearly, custom
            $table->integer('repeat_interval')->nullable(); // напр. 3 за всеки 3 месеца
            $table->string('repeat_unit')->nullable(); // days, months, years
            $table->integer('period_start_day')->nullable(); // напр. 3
            $table->integer('period_end_day')->nullable(); // напр. 18
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_transaction_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recurring_payments');
    }
};

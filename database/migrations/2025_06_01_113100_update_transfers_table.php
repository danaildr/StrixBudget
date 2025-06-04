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
        Schema::table('transfers', function (Blueprint $table) {
            // Добавяне на user_id
            $table->foreignId('user_id')->after('id')->constrained()->onDelete('cascade');
            
            // Преименуване на amount на amount_from и добавяне на currency_from
            $table->renameColumn('amount', 'amount_from');
            $table->string('currency_from')->after('amount_from');
            
            // Добавяне на amount_to и currency_to
            $table->decimal('amount_to', 15, 2)->after('currency_from');
            $table->string('currency_to')->after('amount_to');
            
            // Добавяне на exchange_rate
            $table->decimal('exchange_rate', 15, 4)->after('currency_to');
            
            // Преименуване и промяна на типа на date
            $table->renameColumn('date', 'executed_at');
            $table->dateTime('executed_at')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transfers', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'currency_from', 'amount_to', 'currency_to', 'exchange_rate']);
            $table->renameColumn('amount_from', 'amount');
            $table->renameColumn('executed_at', 'date');
            $table->date('date')->change();
        });
    }
}; 
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
        Schema::table('transactions', function (Blueprint $table) {
            $table->index('executed_at');
        });

        Schema::table('transfers', function (Blueprint $table) {
            $table->index('executed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['executed_at']);
        });

        Schema::table('transfers', function (Blueprint $table) {
            $table->dropIndex(['executed_at']);
        });
    }
};

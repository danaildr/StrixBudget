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
        // Add indexes to transactions table for better performance
        Schema::table('transactions', function (Blueprint $table) {
            // Composite index for user + date filtering (most common query)
            $table->index(['user_id', 'executed_at'], 'idx_transactions_user_date');
            
            // Index for transaction type filtering
            $table->index('type', 'idx_transactions_type');
            
            // Composite index for user + type filtering
            $table->index(['user_id', 'type'], 'idx_transactions_user_type');
            
            // Composite index for bank account + date filtering
            $table->index(['bank_account_id', 'executed_at'], 'idx_transactions_account_date');
            
            // Index for counterparty filtering
            $table->index('counterparty_id', 'idx_transactions_counterparty');
            
            // Index for transaction type filtering
            $table->index('transaction_type_id', 'idx_transactions_transaction_type');
        });

        // Add indexes to transfers table for better performance
        Schema::table('transfers', function (Blueprint $table) {
            // Composite index for user + date filtering
            $table->index(['user_id', 'executed_at'], 'idx_transfers_user_date');
            
            // Index for from account filtering
            $table->index('from_account_id', 'idx_transfers_from_account');
            
            // Index for to account filtering
            $table->index('to_account_id', 'idx_transfers_to_account');
        });

        // Add indexes to counterparties table for better search performance
        Schema::table('counterparties', function (Blueprint $table) {
            // Index for user filtering
            $table->index('user_id', 'idx_counterparties_user');
            
            // Index for name searching (if not already exists)
            $table->index('name', 'idx_counterparties_name');
        });

        // Add indexes to transaction_types table
        Schema::table('transaction_types', function (Blueprint $table) {
            // Index for user filtering
            $table->index('user_id', 'idx_transaction_types_user');
            
            // Index for name searching
            $table->index('name', 'idx_transaction_types_name');
        });

        // Add indexes to bank_accounts table
        Schema::table('bank_accounts', function (Blueprint $table) {
            // Index for user filtering
            $table->index('user_id', 'idx_bank_accounts_user');
            
            // Index for active accounts
            $table->index('is_active', 'idx_bank_accounts_active');
            
            // Index for default account
            $table->index('is_default', 'idx_bank_accounts_default');
            
            // Composite index for user + active filtering
            $table->index(['user_id', 'is_active'], 'idx_bank_accounts_user_active');
        });

        // Add indexes to users table for admin queries
        Schema::table('users', function (Blueprint $table) {
            // Index for role filtering
            $table->index('role', 'idx_users_role');
            
            // Index for creation date ordering
            $table->index('created_at', 'idx_users_created_at');
        });

        // Add indexes to registration_keys table
        Schema::table('registration_keys', function (Blueprint $table) {
            // Index for usage status
            $table->index('is_used', 'idx_registration_keys_used');
            
            // Index for creator
            $table->index('created_by', 'idx_registration_keys_creator');
            
            // Index for used by
            $table->index('used_by', 'idx_registration_keys_used_by');
            
            // Index for creation date ordering
            $table->index('created_at', 'idx_registration_keys_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('idx_transactions_user_date');
            $table->dropIndex('idx_transactions_type');
            $table->dropIndex('idx_transactions_user_type');
            $table->dropIndex('idx_transactions_account_date');
            $table->dropIndex('idx_transactions_counterparty');
            $table->dropIndex('idx_transactions_transaction_type');
        });

        Schema::table('transfers', function (Blueprint $table) {
            $table->dropIndex('idx_transfers_user_date');
            $table->dropIndex('idx_transfers_from_account');
            $table->dropIndex('idx_transfers_to_account');
        });

        Schema::table('counterparties', function (Blueprint $table) {
            $table->dropIndex('idx_counterparties_user');
            $table->dropIndex('idx_counterparties_name');
        });

        Schema::table('transaction_types', function (Blueprint $table) {
            $table->dropIndex('idx_transaction_types_user');
            $table->dropIndex('idx_transaction_types_name');
        });

        Schema::table('bank_accounts', function (Blueprint $table) {
            $table->dropIndex('idx_bank_accounts_user');
            $table->dropIndex('idx_bank_accounts_active');
            $table->dropIndex('idx_bank_accounts_default');
            $table->dropIndex('idx_bank_accounts_user_active');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_role');
            $table->dropIndex('idx_users_created_at');
        });

        Schema::table('registration_keys', function (Blueprint $table) {
            $table->dropIndex('idx_registration_keys_used');
            $table->dropIndex('idx_registration_keys_creator');
            $table->dropIndex('idx_registration_keys_used_by');
            $table->dropIndex('idx_registration_keys_created_at');
        });
    }
};

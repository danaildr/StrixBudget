<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Променяме съществуващите роли от 'limited' на 'power_user'
        DB::table('users')
            ->where('role', 'limited')
            ->update(['role' => 'power_user']);

        // Променяме съществуващите ключове за регистрация от 'limited' на 'power_user'
        DB::table('registration_keys')
            ->where('role', 'limited')
            ->update(['role' => 'power_user']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Връщаме промените
        DB::table('users')
            ->where('role', 'power_user')
            ->update(['role' => 'limited']);

        DB::table('registration_keys')
            ->where('role', 'power_user')
            ->update(['role' => 'limited']);
    }
};

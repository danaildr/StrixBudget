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
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            // За MySQL използваме ENUM
            DB::statement("ALTER TABLE registration_keys MODIFY COLUMN role ENUM('admin', 'user', 'power_user') DEFAULT 'user'");
        } else {
            // За SQLite и други бази данни използваме string колона
            Schema::table('registration_keys', function (Blueprint $table) {
                $table->string('role')->default('user')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            // Връщаме старата структура за MySQL
            DB::statement("ALTER TABLE registration_keys MODIFY COLUMN role ENUM('admin', 'user', 'limited') DEFAULT 'user'");
        } else {
            // За SQLite и други бази данни не правим нищо специално
            Schema::table('registration_keys', function (Blueprint $table) {
                $table->string('role')->default('user')->change();
            });
        }
    }
};

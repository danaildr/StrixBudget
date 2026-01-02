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
        // Insert SMTP settings
        DB::table('system_settings')->insert([
            [
                'key' => 'smtp_host',
                'value' => null,
                'type' => 'string',
                'group' => 'email',
                'description' => 'SMTP сървър (хост)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'smtp_port',
                'value' => null,
                'type' => 'integer',
                'group' => 'email',
                'description' => 'SMTP порт',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'smtp_username',
                'value' => null,
                'type' => 'string',
                'group' => 'email',
                'description' => 'SMTP потребителско име',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'smtp_password',
                'value' => null,
                'type' => 'string',
                'group' => 'email',
                'description' => 'SMTP парола',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'smtp_encryption',
                'value' => null,
                'type' => 'string',
                'group' => 'email',
                'description' => 'SMTP криптиране (tls, ssl)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'smtp_from_address',
                'value' => null,
                'type' => 'string',
                'group' => 'email',
                'description' => 'Email адрес за изпращане',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'smtp_from_name',
                'value' => null,
                'type' => 'string',
                'group' => 'email',
                'description' => 'Име за изпращане',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'smtp_enabled',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'email',
                'description' => 'Активиране на SMTP имейл нотификации',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove SMTP settings
        $smtpKeys = [
            'smtp_host',
            'smtp_port', 
            'smtp_username',
            'smtp_password',
            'smtp_encryption',
            'smtp_from_address',
            'smtp_from_name',
            'smtp_enabled'
        ];
        
        DB::table('system_settings')->whereIn('key', $smtpKeys)->delete();
    }
};

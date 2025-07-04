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
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, text, boolean, integer, file
            $table->string('group')->default('general'); // general, appearance, system
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        DB::table('system_settings')->insert([
            [
                'key' => 'site_name',
                'value' => 'StrixBudget',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Име на сайта',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'site_icon',
                'value' => null,
                'type' => 'file',
                'group' => 'appearance',
                'description' => 'Икона на сайта',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'favicon',
                'value' => null,
                'type' => 'file',
                'group' => 'appearance',
                'description' => 'Favicon на сайта',
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
        Schema::dropIfExists('system_settings');
    }
};

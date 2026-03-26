<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trails', function (Blueprint $table) {
            $table->integer('quota_per_day')->nullable()->after('is_active')
                ->comment('Kuota pendaki per hari untuk jalur ini; null = ikut default mountain_regulations.quota_per_trail_per_day');
        });
    }

    public function down(): void
    {
        Schema::table('trails', function (Blueprint $table) {
            $table->dropColumn('quota_per_day');
        });
    }
};

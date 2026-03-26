<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mountain_regulations', function (Blueprint $table) {
            $table->enum('guide_requirement_level', ['none', 'recommended', 'mandatory', 'expert_only'])
                  ->default('none')
                  ->after('guide_required')
                  ->comment('Level kewajiban pemandu sesuai Permen LHK 13/2024');

            $table->tinyInteger('guide_ratio_max_hikers')
                  ->default(7)
                  ->nullable()
                  ->after('guide_requirement_level')
                  ->comment('Maks pendaki per 1 pemandu; null = tidak ada rasio');
        });
    }

    public function down(): void
    {
        Schema::table('mountain_regulations', function (Blueprint $table) {
            $table->dropColumn(['guide_requirement_level', 'guide_ratio_max_hikers']);
        });
    }
};

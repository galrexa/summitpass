<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mountain_regulations', function (Blueprint $table) {
            $table->integer('min_elevation_experience')->nullable()->after('guide_required')
                ->comment('Ketinggian minimum (MDPL) gunung yang pernah didaki sebagai syarat perizinan. Null = tidak ada syarat.');
        });
    }

    public function down(): void
    {
        Schema::table('mountain_regulations', function (Blueprint $table) {
            $table->dropColumn('min_elevation_experience');
        });
    }
};

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
        Schema::table('trail_checkpoints', function (Blueprint $table) {
            $table->integer('estimated_duration_minutes')
                  ->nullable()
                  ->after('altitude')
                  ->comment('Estimasi waktu tempuh dari pos sebelumnya (menit)');
        });
    }

    public function down(): void
    {
        Schema::table('trail_checkpoints', function (Blueprint $table) {
            $table->dropColumn('estimated_duration_minutes');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trails', function (Blueprint $table) {
            $table->enum('grade', ['I', 'II', 'III', 'IV', 'V'])
                  ->nullable()
                  ->after('name')
                  ->comment('Grade jalur sesuai Permen LHK 13/2024');

            $table->decimal('length_km', 5, 2)
                  ->nullable()
                  ->after('grade')
                  ->comment('Panjang jalur dalam km');

            $table->integer('elevation_gain_m')
                  ->nullable()
                  ->after('length_km')
                  ->comment('Beda tinggi/gain dalam meter');

            $table->enum('surface_type', ['tanah', 'batu', 'pasir', 'campuran'])
                  ->nullable()
                  ->after('elevation_gain_m');
        });
    }

    public function down(): void
    {
        Schema::table('trails', function (Blueprint $table) {
            $table->dropColumn(['grade', 'length_km', 'elevation_gain_m', 'surface_type']);
        });
    }
};

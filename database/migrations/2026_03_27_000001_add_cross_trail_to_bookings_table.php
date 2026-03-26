<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Mendukung fitur lintas jalur (cross-trail):
     *  - trail_id  = jalur NAIK (gate IN)  — kolom existing, tidak berubah semantiknya
     *  - trail_out_id = jalur TURUN (gate OUT) — nullable; jika null => sama dengan trail_id (non-lintas)
     *  - is_cross_trail = flag shortcut untuk query cepat
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('trail_out_id')
                ->nullable()
                ->after('trail_id')
                ->constrained('trails')
                ->restrictOnDelete()
                ->comment('Jalur turun (gate OUT); NULL berarti sama dengan trail_id');

            $table->boolean('is_cross_trail')
                ->default(false)
                ->after('trail_out_id')
                ->comment('true jika pendaki memilih lintas jalur naik/turun berbeda');

            $table->index('trail_out_id');
            $table->index('is_cross_trail');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['trail_out_id']);
            $table->dropIndex(['trail_out_id']);
            $table->dropIndex(['is_cross_trail']);
            $table->dropColumn(['trail_out_id', 'is_cross_trail']);
        });
    }
};

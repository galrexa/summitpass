<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pos seperti "Puncak" atau "Pelawangan" bisa menjadi milik lebih dari satu jalur.
     * Kolom shared_checkpoint_group mengelompokkan checkpoint yang secara fisik sama
     * tapi didaftarkan di trail yang berbeda.
     *
     * Contoh: Pelawangan Sembalun (trail_id=1, order_seq=3) dan
     *         Pelawangan Sembalun (trail_id=2, order_seq=1) memiliki
     *         shared_checkpoint_group = 'pelawangan_sembalun_rinjani'
     *
     * Ini digunakan validator lintas jalur untuk mengenali bahwa
     * scan di satu checkpoint setara dengan scan di checkpoint "saudara"-nya.
     */
    public function up(): void
    {
        Schema::table('trail_checkpoints', function (Blueprint $table) {
            $table->string('shared_checkpoint_group', 100)
                ->nullable()
                ->after('estimated_duration_minutes')
                ->comment('Slug grup untuk checkpoint yang secara fisik sama di jalur berbeda');

            $table->index('shared_checkpoint_group');
        });
    }

    public function down(): void
    {
        Schema::table('trail_checkpoints', function (Blueprint $table) {
            $table->dropIndex(['shared_checkpoint_group']);
            $table->dropColumn('shared_checkpoint_group');
        });
    }
};

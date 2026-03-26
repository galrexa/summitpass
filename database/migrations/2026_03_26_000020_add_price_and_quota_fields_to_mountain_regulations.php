<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mountain_regulations', function (Blueprint $table) {
            // Harga: lokal weekday sudah ada di base_price, tambah 3 lainnya
            $table->decimal('price_weekend', 12, 2)->nullable()->after('base_price')
                ->comment('Harga wisatawan lokal hari Sabtu/Minggu/libur nasional');
            $table->decimal('price_foreign_weekday', 12, 2)->nullable()->after('price_weekend')
                ->comment('Harga wisatawan mancanegara hari kerja');
            $table->decimal('price_foreign_weekend', 12, 2)->nullable()->after('price_foreign_weekday')
                ->comment('Harga wisatawan mancanegara hari Sabtu/Minggu/libur nasional');
            // Kuota total gunung per hari (semua jalur digabung)
            $table->integer('quota_total_per_day')->nullable()->after('quota_per_trail_per_day')
                ->comment('Kuota total pendaki per hari lintas semua jalur; null = tidak dibatasi total');
        });

        // Isi price_weekend default = base_price, foreign = 3× base_price (konvensi umum)
        DB::statement('UPDATE mountain_regulations SET
            price_weekend           = base_price,
            price_foreign_weekday   = base_price * 3,
            price_foreign_weekend   = base_price * 3
        ');
    }

    public function down(): void
    {
        Schema::table('mountain_regulations', function (Blueprint $table) {
            $table->dropColumn(['price_weekend', 'price_foreign_weekday', 'price_foreign_weekend', 'quota_total_per_day']);
        });
    }
};

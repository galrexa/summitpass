<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mountain_regulations', function (Blueprint $table) {
            $table->decimal('price_student', 12, 2)->nullable()->after('price_foreign_weekend')
                ->comment('Harga pelajar/anak (< 17 tahun) hari kerja; null = sama dengan harga lokal weekday');
            $table->decimal('price_student_weekend', 12, 2)->nullable()->after('price_student')
                ->comment('Harga pelajar/anak hari Sabtu/Minggu; null = ikut price_student atau lokal weekend');
            $table->boolean('minor_must_be_accompanied')->default(true)->after('price_student_weekend')
                ->comment('Peserta < 17 tahun wajib didampingi minimal 1 pendaki dewasa (>= 17 tahun) dalam booking yang sama');
        });
    }

    public function down(): void
    {
        Schema::table('mountain_regulations', function (Blueprint $table) {
            $table->dropColumn(['price_student', 'price_student_weekend', 'minor_must_be_accompanied']);
        });
    }
};

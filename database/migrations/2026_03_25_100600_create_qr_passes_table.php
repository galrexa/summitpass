<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qr_passes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_participant_id')->constrained()->cascadeOnDelete();
            $table->string('qr_token')->unique()
                ->comment('Token signed unik per peserta, digunakan untuk generate QR Code');
            $table->timestamp('valid_from')
                ->comment('Aktif mulai hari H pukul 00.00');
            $table->timestamp('valid_until')
                ->comment('Kadaluarsa pada tanggal turun pukul checkout_deadline_hour');
            $table->enum('status', ['inactive', 'active', 'used', 'expired'])->default('inactive');
            $table->timestamps();

            $table->index('qr_token');
            $table->index('status');
            $table->index('valid_until');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_passes');
    }
};

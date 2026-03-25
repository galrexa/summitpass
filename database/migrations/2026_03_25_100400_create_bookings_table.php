<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('leader_user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('mountain_id')->constrained()->restrictOnDelete();
            $table->foreignId('trail_id')->constrained()->restrictOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('booking_code')->unique()->nullable()
                ->comment('Kode booking diterbitkan setelah pembayaran berhasil');
            $table->boolean('guide_requested')->default(false);
            $table->timestamp('tos_accepted_at')->nullable()
                ->comment('Waktu pendaftar menyetujui SOP & TnC');
            $table->enum('status', ['pending_payment', 'paid', 'active', 'completed', 'cancelled'])
                ->default('pending_payment');
            $table->decimal('total_price', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('leader_user_id');
            $table->index('mountain_id');
            $table->index('status');
            $table->index('start_date');
            $table->index(['leader_user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};

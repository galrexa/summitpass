<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete()
                ->comment('Nullable jika peserta belum punya akun saat didaftarkan');
            $table->string('nik', 16)->comment('NIK peserta sesuai KTP');
            $table->string('name');
            $table->enum('role', ['leader', 'member'])->default('member');
            $table->timestamps();

            $table->index('booking_id');
            $table->index('user_id');
            $table->index('nik');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_participants');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trekking_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('qr_pass_id')->constrained()->restrictOnDelete();
            $table->foreignId('trail_checkpoint_id')->constrained()->restrictOnDelete();
            $table->enum('direction', ['up', 'down'])
                ->comment('up = naik/masuk, down = turun/keluar');
            $table->timestamp('scanned_at');
            $table->foreignId('scanned_by_user_id')->nullable()->constrained('users')->nullOnDelete()
                ->comment('Petugas pos atau pendaki sendiri yang scan');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->json('device_info')->nullable()
                ->comment('Info perangkat yang digunakan untuk scan');
            $table->boolean('anomaly_flag')->default(false);
            $table->string('anomaly_reason')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('qr_pass_id');
            $table->index('trail_checkpoint_id');
            $table->index('scanned_at');
            $table->index('anomaly_flag');
            $table->index(['qr_pass_id', 'direction']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trekking_logs');
    }
};

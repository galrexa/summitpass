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
        Schema::create('checkpoint_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained()->onDelete('cascade');
            $table->foreignId('checkpoint_id')->constrained()->onDelete('restrict');
            $table->timestamp('logged_at');
            $table->decimal('latitude', 10, 8)->nullable(); // GPS latitude
            $table->decimal('longitude', 11, 8)->nullable(); // GPS longitude
            $table->json('device_info')->nullable(); // Device type, OS, version, etc
            $table->boolean('verified')->default(false);
            $table->boolean('anomaly_flag')->default(false);
            $table->string('anomaly_reason')->nullable();
            $table->timestamp('created_at')->useCurrent();

            // Indexes for queries
            $table->index('trip_id');
            $table->index('checkpoint_id');
            $table->index('logged_at');
            $table->index('anomaly_flag');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checkpoint_logs');
    }
};
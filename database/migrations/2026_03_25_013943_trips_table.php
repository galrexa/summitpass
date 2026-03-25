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
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('mountain_id')->constrained()->onDelete('restrict');
            $table->foreignId('basecamp_id')->constrained()->onDelete('restrict');
            $table->foreignId('operator_id')->nullable()->constrained('users')->onDelete('set null');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['pending', 'active', 'completed', 'cancelled'])->default('pending');
            $table->integer('total_price');
            $table->string('booking_reference')->unique()->nullable();
            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('mountain_id');
            $table->index('status');
            $table->index('start_date');
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
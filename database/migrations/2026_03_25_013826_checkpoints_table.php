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
        Schema::create('checkpoints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mountain_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('order_seq');
            $table->enum('type', ['station', 'terminal']);
            $table->decimal('latitude', 10, 8); // Latitude coordinate
            $table->decimal('longitude', 11, 8); // Longitude coordinate
            $table->integer('altitude');
            $table->timestamps();

            // Indexes for location queries
            $table->index('mountain_id');
            $table->index(['latitude', 'longitude']);
            $table->unique(['mountain_id', 'order_seq']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checkpoints');
    }
};
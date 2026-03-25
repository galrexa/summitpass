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
        Schema::create('mountains', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location');
            $table->integer('height_mdpl');
            $table->enum('difficulty', ['Easy', 'Moderate', 'Hard']);
            $table->integer('min_level')->default(1);
            $table->integer('base_price');
            $table->integer('max_days')->default(2);
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('difficulty');
            $table->index('min_level');
            $table->fullText(['name', 'location', 'description']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mountains');
    }
};
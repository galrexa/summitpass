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
        Schema::create('basecamps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mountain_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('route_order');
            $table->timestamps();

            // Indexes
            $table->index('mountain_id');
            $table->unique(['mountain_id', 'route_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('basecamps');
    }
};
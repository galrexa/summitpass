<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trail_checkpoints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trail_id')->constrained()->cascadeOnDelete();
            $table->foreignId('mountain_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('order_seq');
            $table->enum('type', ['gate_in', 'pos', 'summit', 'gate_out']);
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->integer('altitude')->nullable()->comment('Ketinggian dalam meter');
            $table->timestamps();

            $table->unique(['trail_id', 'order_seq']);
            $table->index(['trail_id', 'type']);
            $table->index(['latitude', 'longitude']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trail_checkpoints');
    }
};

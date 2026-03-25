<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mountain_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('route_order')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['mountain_id', 'route_order']);
            $table->index('mountain_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trails');
    }
};

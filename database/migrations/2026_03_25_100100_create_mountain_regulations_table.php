<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mountain_regulations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mountain_id')->unique()->constrained()->cascadeOnDelete();
            $table->decimal('base_price', 12, 2)->default(0);
            $table->integer('quota_per_trail_per_day')->default(50);
            $table->integer('max_hiking_days')->default(3);
            $table->integer('max_participants_per_account')->default(10);
            $table->boolean('guide_required')->default(false);
            $table->tinyInteger('checkout_deadline_hour')->default(17)
                ->comment('Jam batas checkout (format 24 jam, default 17.00 WIB)');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mountain_regulations');
    }
};

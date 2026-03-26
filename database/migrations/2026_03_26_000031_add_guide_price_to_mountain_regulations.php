<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mountain_regulations', function (Blueprint $table) {
            $table->decimal('guide_price_per_day', 12, 2)->nullable()->after('guide_required')
                ->comment('Biaya jasa guide per hari (flat, bukan per orang); null = gratis / tidak ada guide');
        });
    }

    public function down(): void
    {
        Schema::table('mountain_regulations', function (Blueprint $table) {
            $table->dropColumn('guide_price_per_day');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mountains', function (Blueprint $table) {
            $table->enum('trail_status', ['open', 'closed', 'caution'])->default('open')->after('is_active');
            $table->string('ecosystem_type')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('mountains', function (Blueprint $table) {
            $table->dropColumn(['trail_status', 'ecosystem_type']);
        });
    }
};

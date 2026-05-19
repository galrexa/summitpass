<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('qr_passes', function (Blueprint $table) {
            $table->string('family_token', 64)->nullable()->unique()->after('status')
                ->comment('Token unik untuk akses publik keluarga');
        });
    }

    public function down(): void
    {
        Schema::table('qr_passes', function (Blueprint $table) {
            $table->dropColumn('family_token');
        });
    }
};

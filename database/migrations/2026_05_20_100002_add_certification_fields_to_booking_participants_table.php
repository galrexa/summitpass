<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_participants', function (Blueprint $table) {
            $table->string('role')->default('hiker')->change();
            $table->string('certification_number')->nullable()->after('role')
                ->comment('Nomor sertifikat APGI untuk guide');
            $table->string('affiliation')->nullable()->after('certification_number')
                ->comment('Nama operator/komunitas');
        });
    }

    public function down(): void
    {
        Schema::table('booking_participants', function (Blueprint $table) {
            $table->dropColumn(['certification_number', 'affiliation']);
            $table->enum('role', ['leader', 'member'])->default('member')->change();
        });
    }
};

<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mountains', function (Blueprint $table) {
            $table->foreignId('pengelola_id')->nullable()->after('is_active')
                  ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('mountains', function (Blueprint $table) {
            $table->dropForeign(['pengelola_id']);
            $table->dropColumn('pengelola_id');
        });
    }
};

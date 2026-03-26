<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // difficulty was already dropped; just enforce grade as NOT NULL
        DB::statement("UPDATE mountains SET grade = 'I' WHERE grade IS NULL");
        DB::statement("ALTER TABLE mountains MODIFY grade ENUM('I','II','III','IV','V') NOT NULL DEFAULT 'I'");
    }

    public function down(): void
    {
        Schema::table('mountains', function (Blueprint $table) {
            $table->enum('difficulty', ['Easy', 'Moderate', 'Hard'])->after('height_mdpl')->default('Moderate');
        });

        DB::statement("ALTER TABLE mountains MODIFY grade ENUM('I','II','III','IV','V') NULL DEFAULT NULL");
    }
};

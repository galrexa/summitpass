<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE payments MODIFY COLUMN gateway ENUM('midtrans','xendit','manual','simulate') NOT NULL DEFAULT 'midtrans'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE payments MODIFY COLUMN gateway ENUM('midtrans','xendit','manual') NOT NULL DEFAULT 'midtrans'");
    }
};

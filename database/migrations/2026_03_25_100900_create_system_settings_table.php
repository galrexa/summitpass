<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Seed default settings
        DB::table('system_settings')->insert([
            [
                'key'         => 'anomaly_check_interval_minutes',
                'value'       => '30',
                'description' => 'Interval (menit) scheduler cek anomali — pendaki belum checkout setelah batas waktu',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'key'         => 'anomaly_stall_threshold_hours',
                'value'       => '6',
                'description' => 'Threshold (jam) tidak ada log scan baru sebelum dianggap anomali terhenti (opsional)',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'key'         => 'anomaly_checkout_grace_minutes',
                'value'       => '0',
                'description' => 'Grace period (menit) setelah checkout_deadline_hour sebelum alert dikirim',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};

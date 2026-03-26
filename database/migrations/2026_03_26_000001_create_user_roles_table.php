<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name');
            $table->timestamps();
        });

        DB::table('user_roles')->insert([
            ['name' => 'pendaki',      'display_name' => 'Pendaki',                    'created_at' => now(), 'updated_at' => now()],
            ['name' => 'pengelola_tn', 'display_name' => 'Pengelola Taman Nasional',   'created_at' => now(), 'updated_at' => now()],
            ['name' => 'admin',        'display_name' => 'Administrator',              'created_at' => now(), 'updated_at' => now()],
            ['name' => 'officer',      'display_name' => 'Petugas Lapangan',           'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('user_roles');
    }
};

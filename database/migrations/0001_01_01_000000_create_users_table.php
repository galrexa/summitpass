<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('nik', 16)->unique()->nullable()->comment('NIK KTP untuk WNI');
            $table->string('passport_number', 20)->unique()->nullable()->comment('Nomor paspor untuk WNA');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['pendaki', 'pengelola_tn', 'admin', 'officer'])->default('pendaki');
            $table->rememberToken();
            $table->timestamps();

            $table->index('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
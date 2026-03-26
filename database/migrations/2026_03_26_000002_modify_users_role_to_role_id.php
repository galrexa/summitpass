<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add role_id as nullable first
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id')->nullable()->after('password');
            $table->foreign('role_id')->references('id')->on('user_roles');
        });

        // Populate role_id from existing role enum
        DB::statement("UPDATE users u JOIN user_roles ur ON ur.name = u.role SET u.role_id = ur.id");

        // Make role_id NOT NULL using raw SQL (avoids doctrine/dbal dependency)
        DB::statement("ALTER TABLE users MODIFY role_id BIGINT UNSIGNED NOT NULL");

        // Drop old role column and its index
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_role_index');
            $table->dropColumn('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['pendaki', 'pengelola_tn', 'admin', 'officer'])->default('pendaki')->after('password');
        });

        DB::statement("UPDATE users u JOIN user_roles ur ON ur.id = u.role_id SET u.role = ur.name");

        Schema::table('users', function (Blueprint $table) {
            $table->index('role');
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });
    }
};

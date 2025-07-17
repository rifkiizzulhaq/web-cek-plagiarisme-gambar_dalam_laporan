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
        Schema::table('users', function (Blueprint $table) {
            $table->string('nim')->unique()->nullable()->after('role_id');
            $table->string('prodi')->nullable()->after('nim');
            $table->string('angkatan')->nullable()->after('prodi');
            $table->string('kelas')->nullable()->after('angkatan');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nim', 'prodi', 'angkatan', 'kelas']);
        });
    }
};

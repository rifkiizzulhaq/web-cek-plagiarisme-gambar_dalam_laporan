<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('files', function (Blueprint $table) {
            $table->dropColumn('image_plagiarism_report');
        });
    }

    public function down(): void
    {
        Schema::table('files', function (Blueprint $table) {
            $table->json('image_plagiarism_report')->nullable()->after('result');
        });
    }
};

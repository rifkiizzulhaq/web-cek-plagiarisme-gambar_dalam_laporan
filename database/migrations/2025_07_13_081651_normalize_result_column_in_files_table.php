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
        Schema::table('files', function (Blueprint $table) {
            $table->integer('total_sentences')->nullable()->after('status');
            $table->integer('plagiarized_sentences')->nullable()->after('total_sentences');
            $table->float('similarity_percentage')->nullable()->after('plagiarized_sentences');
            $table->dropColumn('result');
        });
    }

    public function down(): void
    {
        Schema::table('files', function (Blueprint $table) {
            $table->dropColumn(['total_sentences', 'plagiarized_sentences', 'similarity_percentage']);
            $table->json('result')->nullable()->after('status');
        });
    }
};

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
        Schema::create('image_plagiarism_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_id')->constrained()->onDelete('cascade');
            $table->integer('source_image_index');
            $table->string('source_image');
            $table->string('match_image');
            $table->text('match_doc_title');
            $table->float('similarity', 5, 4); // 5 total digit, 4 di belakang koma (contoh: 0.9876)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('image_plagiarism_reports');
    }
};

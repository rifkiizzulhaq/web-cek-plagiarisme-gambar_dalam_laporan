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
        Schema::create('image_embeddings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doc_id')->constrained('document_hashes')->onDelete('cascade');
            $table->string('image_name')->index();
            $table->binary('embedding');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('image_embeddings');
    }
};

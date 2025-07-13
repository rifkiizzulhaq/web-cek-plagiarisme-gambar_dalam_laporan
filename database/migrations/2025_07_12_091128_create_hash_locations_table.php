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
        Schema::create('hash_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hash_id')->constrained('sentence_hashes')->onDelete('cascade');
            $table->foreignId('doc_id')->constrained('document_hashes')->onDelete('cascade');
            $table->integer('page_number');
            $table->timestamp('created_at')->useCurrent();
            $table->unique(['hash_id', 'doc_id', 'page_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hash_locations');
    }
};

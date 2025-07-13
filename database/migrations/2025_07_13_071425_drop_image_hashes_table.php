<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Perintah ini akan menghapus tabel 'image_hashes' jika ada.
        Schema::dropIfExists('image_hashes');
    }

    public function down(): void
    {
        Schema::create('image_hashes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_id')->constrained()->onDelete('cascade');
            $table->string('image_name');
            $table->string('hash_value', 64);
            $table->timestamps();
            $table->index('hash_value');
        });
    }
};

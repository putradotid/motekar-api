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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('filename');                          // nama file asli
            $table->string('path');                              // path di storage
            $table->string('url');                               // URL publik
            $table->string('category')->default('image');        // image/icon/team/clients/background
            $table->string('mime_type')->nullable();             // image/png, image/jpg, dll
            $table->unsignedBigInteger('size')->nullable();      // ukuran file dalam bytes
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade'); // uploader
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};

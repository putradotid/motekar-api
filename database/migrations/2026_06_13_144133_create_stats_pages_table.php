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
        Schema::create('stats_pages', function (Blueprint $table) {
            $table->id();
            $table->string('label_1'); // Klien Terlayani
            $table->string('value_1');
            $table->string('label_2'); // Proyek Selesai
            $table->string('value_2');
            $table->string('label_3'); // Tenaga Profesional
            $table->string('value_3');
            $table->string('label_4'); // Mitra & Kolaborasi
            $table->string('value_4');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stats_pages');
    }
};

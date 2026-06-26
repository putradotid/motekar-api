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
        // Tentang Kami
        Schema::table('about_us_pages', function (Blueprint $table) {
            $table->string('background_type')->default('color')->after('description');
            $table->string('background_value')->default('#D1D5DB')->after('background_type');
        });

        // Produk & Layanan
        Schema::table('produk_layanan_pages', function (Blueprint $table) {
            $table->string('background_type')->default('color')->after('description');
            $table->string('background_value')->default('#D1D5DB')->after('background_type');
        });

        // Testimoni
        Schema::table('testimoni_pages', function (Blueprint $table) {
            $table->string('background_type')->default('color')->after('description');
            $table->string('background_value')->default('#F8E19A')->after('background_type');
        });

        // Tim Kami
        Schema::table('tim_kami_pages', function (Blueprint $table) {
            $table->string('background_type')->default('color')->after('description');
            $table->string('background_value')->default('#D1D5DB')->after('background_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('about_us_pages', function (Blueprint $table) {
            $table->dropColumn(['background_type', 'background_value']);
        });
        Schema::table('produk_layanan_pages', function (Blueprint $table) {
            $table->dropColumn(['background_type', 'background_value']);
        });
        Schema::table('testimoni_pages', function (Blueprint $table) {
            $table->dropColumn(['background_type', 'background_value']);
        });
        Schema::table('tim_kami_pages', function (Blueprint $table) {
            $table->dropColumn(['background_type', 'background_value']);
        });
    }
};

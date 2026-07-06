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
        Schema::table('testimonials', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            $table->unsignedBigInteger('meeting_id')->nullable()->after('user_id');
            $table->integer('rating')->default(5)->after('social_handle');
            $table->string('position')->nullable()->after('name');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('rating');
            $table->text('admin_notes')->nullable()->after('status');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('meeting_id')->references('id')->on('meeting_requests')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('testimonials', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['meeting_id']);
            $table->dropColumn(['user_id', 'meeting_id', 'rating', 'position', 'status', 'admin_notes']);
        });
    }
};

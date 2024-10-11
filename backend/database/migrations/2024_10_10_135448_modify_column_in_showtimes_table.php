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
        Schema::table('showtimes', function (Blueprint $table) {
            //
            $table->dropForeign(['phim_id']); // Xóa khóa ngoại cũ nếu tồn tại
            $table->dropForeign(['rapphim_id']); // Xóa khóa ngoại cũ nếu tồn tại
            $table->dropForeign(['room_id']); // Xóa khóa ngoại cũ nếu tồn tại

            $table->foreign('phim_id')->references('id')->on('movies')->onDelete('cascade')->change();  // Khóa ngoại
            $table->foreign('rapphim_id')->references('id')->on('theaters')->onDelete('cascade')->change();  // Khóa ngoại
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade')->change();  // Khóa ngoại
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('showtimes', function (Blueprint $table) {
            //
        });
    }
};

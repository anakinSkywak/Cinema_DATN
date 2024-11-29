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
        Schema::table('type_blogs', function (Blueprint $table) {
            $table->string('anh')->nullable();  // Thêm trường ảnh
            $table->date('ngay')->nullable();   // Thêm trường ngày
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('type_blogs', function (Blueprint $table) {
            $table->dropColumn(['anh', 'ngay']);  // Xóa các trường khi rollback
        });
    }
};
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
        Schema::table('memberships', function (Blueprint $table) {
            //
            $table->string('so_the')->nullable(); // Thêm cột so_the
            $table->date('ngay_het_han')->nullable(); // Thêm cột ngay_het_han kiểu DATE
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('memberships', function (Blueprint $table) {
            //
            $table->dropColumn('so_the');
            $table->dropColumn('ngay_het_han');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('memberships', function (Blueprint $table) {
            $table->string('so_the')->nullable()->after('ngay_dang_ky');
            $table->date('ngay_het_han')->nullable()->after('ngay_dang_ky');  
        });
    }

    public function down()
    {
        Schema::table('memberships', function (Blueprint $table) {
            $table->dropColumn(['so_the', 'ngay_het_han']); // Xóa các cột nếu rollback
        });
    }
};

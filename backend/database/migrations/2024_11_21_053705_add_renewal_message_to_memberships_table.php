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
            $table->string('renewal_message')->nullable(); // Thêm cột renewal_message
        });
    }

    public function down()
    {
        Schema::table('memberships', function (Blueprint $table) {
            $table->dropColumn('renewal_message'); // Xóa cột nếu rollback
        });
    }
};

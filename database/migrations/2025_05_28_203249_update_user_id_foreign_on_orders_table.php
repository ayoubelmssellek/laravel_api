<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUserIdForeignOnOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // 1. حذف المفتاح الخارجي الحالي
            $table->dropForeign(['user_id']);

            // 2. إعادة إنشاء المفتاح الخارجي مع onDelete('cascade')
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            // إلغاء المفتاح الجديد
            $table->dropForeign(['user_id']);

            // إعادة المفتاح القديم بدون cascade
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->nullOnDelete(); // أو خليه كما كان إذا ما كانش فيه حاجة
        });
    }
}


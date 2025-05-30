<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['order', 'contact', 'review']);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->text('message')->nullable();
            $table->boolean('is_read')->default(false);
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
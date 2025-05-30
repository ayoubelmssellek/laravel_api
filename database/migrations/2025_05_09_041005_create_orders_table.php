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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->decimal('total_order', 8, 2)->nullable();
            $table->string('name');
            $table->string('phonenumber');
            $table->enum('delivery_type', ['delivery', 'pickup']);
            $table->string('street')->nullable();
            $table->string('housenumber')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

 
};

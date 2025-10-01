<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('helper_id')->constrained('users');
            $table->foreignId('cashier_id')->nullable()->constrained('users');
            $table->enum('status', ['preparing', 'ready', 'completed', 'cancelled'])->default('preparing');
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('cash_received', 10, 2)->default(0);
            $table->decimal('change', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
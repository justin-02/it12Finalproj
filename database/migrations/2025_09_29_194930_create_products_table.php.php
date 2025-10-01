<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->string('brand');
            $table->decimal('price', 10, 2);
            $table->integer('current_stock_sacks')->default(0);
            $table->integer('current_stock_pieces')->default(0);
            $table->integer('critical_level_sacks')->default(2);
            $table->integer('critical_level_pieces')->default(10);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            // Change current_stock_sacks from integer to decimal to support fractional sacks
            $table->decimal('current_stock_sacks', 10, 2)->default(0)->change();
            $table->decimal('critical_level_sacks', 10, 2)->default(2)->change();
        });

        Schema::table('inventory_transactions', function (Blueprint $table) {
            // Change quantity_sacks from integer to decimal to support fractional sacks
            $table->decimal('quantity_sacks', 10, 2)->default(0)->change();
            $table->decimal('quantity_kilos', 10, 2)->default(0)->change();
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('current_stock_sacks')->default(0)->change();
            $table->integer('critical_level_sacks')->default(2)->change();
        });

        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->integer('quantity_sacks')->default(0)->change();
            $table->integer('quantity_kilos')->default(0)->change();
        });
    }
};

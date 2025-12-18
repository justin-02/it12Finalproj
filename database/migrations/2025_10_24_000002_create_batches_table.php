<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   // In the migration file
public function up()
{
    Schema::create('batches', function (Blueprint $table) {
        $table->id();
        $table->string('batch_code')->unique();
        $table->date('restock_date');
        $table->date('expiry_date')->nullable();
        $table->string('supplier')->nullable();
        $table->text('notes')->nullable();
        $table->timestamps();
    });
    
    Schema::create('batch_products', function (Blueprint $table) {
        $table->id();
        $table->foreignId('batch_id')->constrained()->onDelete('cascade');
        $table->foreignId('product_id')->constrained()->onDelete('cascade');
        $table->decimal('quantity', 10, 2);
        $table->timestamps();
    });
}
};

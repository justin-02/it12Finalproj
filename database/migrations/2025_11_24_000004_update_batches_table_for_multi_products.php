<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            if (Schema::hasColumn('batches', 'product_id')) {
                $table->dropConstrainedForeignId('product_id');
            }
            if (Schema::hasColumn('batches', 'quantity')) {
                $table->dropColumn('quantity');
            }
        });
    }

    public function down(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->after('id')->constrained('products')->cascadeOnDelete();
            $table->integer('quantity')->default(0)->after('batch_code');
        });
    }
};


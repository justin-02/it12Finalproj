<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('inventory_transactions', 'batch_id')) {
            Schema::table('inventory_transactions', function (Blueprint $table) {
                $table->dropForeign('inventory_transactions_batch_id_foreign');
            });

            Schema::table('inventory_transactions', function (Blueprint $table) {
                $table->foreign('batch_id')->references('id')->on('batches')->nullOnDelete();
            });
        } else {
            Schema::table('inventory_transactions', function (Blueprint $table) {
                $table->foreignId('batch_id')->nullable()->after('product_id')->constrained('batches')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('inventory_transactions', 'batch_id')) {
            Schema::table('inventory_transactions', function (Blueprint $table) {
                $table->dropForeign('inventory_transactions_batch_id_foreign');
            });

            Schema::table('inventory_transactions', function (Blueprint $table) {
                $table->foreign('batch_id')->references('id')->on('product_batches')->nullOnDelete();
            });
        }
    }
};


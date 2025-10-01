<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, let's check what data exists and clean it up
        $orders = DB::table('orders')->get(['id', 'status']);
        
        // Update any invalid status values to valid ones
        foreach ($orders as $order) {
            $validStatus = 'preparing'; // default
            
            // Map any invalid status to valid ones
            switch (strtolower(trim($order->status))) {
                case 'preparing':
                case 'ready':
                case 'completed':
                case 'cancelled':
                    $validStatus = strtolower(trim($order->status));
                    break;
                default:
                    $validStatus = 'preparing'; // default for any invalid status
                    break;
            }
            
            DB::table('orders')
                ->where('id', $order->id)
                ->update(['status' => $validStatus]);
        }
        
        // Now modify the column to be enum
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', ['preparing', 'ready', 'completed', 'cancelled'])
                  ->default('preparing')
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('status')->change();
        });
    }
};

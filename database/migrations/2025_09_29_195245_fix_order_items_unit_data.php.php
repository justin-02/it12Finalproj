<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Fix corrupted unit data
        DB::table('order_items')->whereNotIn('unit', ['sack', 'kilo', 'piece'])->update([
            'unit' => 'sack' // Set a default value
        ]);
    }

    public function down()
    {
        // This migration cannot be reversed safely
    }
};
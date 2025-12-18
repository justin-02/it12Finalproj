<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        // Switch `event` from enum(login,logout) to a VARCHAR to allow arbitrary event strings
        // Using raw statement to avoid requiring doctrine/dbal
        DB::statement("ALTER TABLE `employee_logs` MODIFY `event` VARCHAR(255) NULL");
    }

    public function down(): void
    {
        // Revert to enum('login','logout') - note: may fail if other values exist
        DB::statement("ALTER TABLE `employee_logs` MODIFY `event` ENUM('login','logout') NOT NULL");
    }
};

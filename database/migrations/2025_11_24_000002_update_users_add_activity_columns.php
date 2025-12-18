<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 30)->default('helper')->after('password');
            $table->timestamp('last_login_at')->nullable()->after('role');
            $table->timestamp('last_logout_at')->nullable()->after('last_login_at');
            $table->timestamp('last_seen')->nullable()->after('last_logout_at');
            $table->unsignedInteger('login_count')->default(0)->after('last_seen');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['login_count', 'last_seen', 'last_logout_at', 'last_login_at', 'role']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'inventory', 'cashier', 'helper'])->default('helper')->after('password');
        });
    }
};


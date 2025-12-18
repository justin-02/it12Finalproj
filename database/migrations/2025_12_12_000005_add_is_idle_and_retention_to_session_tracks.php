<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('session_tracks', function (Blueprint $table) {
            if (! Schema::hasColumn('session_tracks', 'is_idle')) {
                $table->boolean('is_idle')->default(false)->after('last_activity_at');
            }
            if (! Schema::hasColumn('session_tracks', 'retention_days')) {
                $table->unsignedInteger('retention_days')->default(90)->after('is_idle');
            }
        });
    }

    public function down(): void
    {
        Schema::table('session_tracks', function (Blueprint $table) {
            if (Schema::hasColumn('session_tracks', 'retention_days')) {
                $table->dropColumn('retention_days');
            }
            if (Schema::hasColumn('session_tracks', 'is_idle')) {
                $table->dropColumn('is_idle');
            }
        });
    }
};

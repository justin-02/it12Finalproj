<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmployeeLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event',
        'logged_at',
        'ip_address',
        'user_agent',
        'session_id',
        'context',
    ];

    protected $casts = [
        'logged_at' => 'datetime',
        'context' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Safely write an employee log. If the `employee_logs` table or the
     * `event` column type would reject the value (enum), this will fallback
     * to writing the log into the application log instead of raising a SQL warning.
     *
     * Returns the created model or null when a DB insert was skipped.
     */
    public static function safeCreate(array $attributes)
    {
        try {
            if (! Schema::hasTable('employee_logs')) {
                Log::warning('employee_logs table missing; skipping DB write', ['attributes' => $attributes]);
                return null;
            }

            // Detect column type for `event`
            try {
                $column = DB::selectOne("SHOW COLUMNS FROM `employee_logs` WHERE Field='event'");
            } catch (\Throwable $e) {
                $column = null;
            }

            $isEnum = false;
            $allowed = null;
            if ($column && isset($column->Type)) {
                $type = strtolower($column->Type);
                $isEnum = str_starts_with($type, 'enum(');
                if ($isEnum) {
                    // parse enum values like: enum('login','logout')
                    preg_match_all("/'([^']+)'/", $column->Type, $m);
                    $allowed = isset($m[1]) ? $m[1] : [];
                }
            }

            // If enum and provided event is not allowed, fallback to app log
            if ($isEnum && isset($attributes['event'])) {
                $evt = (string) $attributes['event'];
                if (! in_array($evt, $allowed, true)) {
                    Log::info('EmployeeLog event not allowed by enum; writing to app log instead', ['event' => $evt, 'attributes' => $attributes]);
                    return null;
                }
            }

            return self::create($attributes);
        } catch (\Throwable $e) {
            Log::error('Failed to write EmployeeLog to DB: '.$e->getMessage(), ['attributes' => $attributes]);
            return null;
        }
    }
}


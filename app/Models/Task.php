<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'task_type',
        'context',
        'started_at',
        'completed_at',
        'duration_seconds',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'context' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Safely create a Task row if the tasks table exists.
     * Returns the created Task model or null on failure.
     */
    public static function safeCreate(array $attributes)
    {
        try {
            if (! Schema::hasTable('tasks')) {
                Log::warning('Attempted to create Task but tasks table does not exist.', ['attributes' => $attributes]);
                return null;
            }

            return self::create($attributes);
        } catch (\Throwable $e) {
            Log::error('Failed to create Task: ' . $e->getMessage(), ['attributes' => $attributes]);
            return null;
        }
    }
}

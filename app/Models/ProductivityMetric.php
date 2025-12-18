<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductivityMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'metrics',
    ];

    protected $casts = [
        'date' => 'date',
        'metrics' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

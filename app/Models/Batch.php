<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Batch extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_code',
        'restock_date',
        'expiry_date',
        'supplier',
        'notes'
    ];

    protected $casts = [
        'restock_date' => 'date',
        'expiry_date' => 'date',
    ];

    // Relationship with products
    public function products()
    {
        return $this->belongsToMany(Product::class, 'batch_product')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }

    // Accessor for total quantity
    public function getTotalQuantityAttribute()
    {
        if (!Schema::hasTable('batch_product')) {
            return 0;
        }
        
        return $this->products->sum('pivot.quantity');
    }
}
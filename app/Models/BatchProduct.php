<?php
// app/Models/BatchProduct.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class BatchProduct extends Model
{
    use HasFactory;

    protected $table = 'batch_products';

    protected $fillable = [
        'batch_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_price',
        'remaining_quantity',
        'unit_type'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'remaining_quantity' => 'decimal:2'
    ];

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Accessors
    public function getIsDepletedAttribute(): bool
    {
        return $this->remaining_quantity <= 0;
    }

    public function getConsumedQuantityAttribute(): float
    {
        return $this->quantity - $this->remaining_quantity;
    }

    public function getConsumedPercentageAttribute(): float
    {
        if ($this->quantity <= 0) {
            return 0;
        }
        return ($this->consumed_quantity / $this->quantity) * 100;
    }

    // Helper methods
    public function consume(float $amount): bool
    {
        if ($amount > $this->remaining_quantity) {
            return false;
        }
        
        $this->remaining_quantity -= $amount;
        $this->save();
        
        // Update batch status if needed
        $this->batch->updateStatus();
        
        return true;
    }

    public function restock(float $amount): void
    {
        $this->quantity += $amount;
        $this->remaining_quantity += $amount;
        $this->total_price = $this->quantity * $this->unit_price;
        $this->save();
        
        // Update batch
        $this->batch->total_cost = $this->batch->batchProducts()->sum('total_price');
        $this->batch->updateStatus();
        $this->batch->save();
    }
}
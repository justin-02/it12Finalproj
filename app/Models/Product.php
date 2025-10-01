<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_name', 'brand', 'price', 'current_stock_sacks', 
        'current_stock_pieces', 'critical_level_sacks', 'critical_level_pieces', 'is_active'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'current_stock_sacks' => 'decimal:2',
        'critical_level_sacks' => 'decimal:2',
        'price' => 'decimal:2',
    ];

    public function inventoryTransactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getTotalStockAttribute()
    {
        return [
            'sacks' => $this->current_stock_sacks,
            'pieces' => $this->current_stock_pieces
        ];
    }

    // Updated to only check critical level for units that have stock
    public function getIsCriticalAttribute()
    {
        // Check if product has sacks and they are critical
        $sacksCritical = $this->current_stock_sacks > 0 && 
                         $this->current_stock_sacks <= $this->critical_level_sacks;
        
        // Check if product has pieces and they are critical  
        $piecesCritical = $this->current_stock_pieces > 0 && 
                          $this->current_stock_pieces <= $this->critical_level_pieces;
        
        return $sacksCritical || $piecesCritical;
    }

    // New method to get which specific unit is critical
    public function getCriticalUnitsAttribute()
    {
        $criticalUnits = [];
        
        if ($this->current_stock_sacks > 0 && $this->current_stock_sacks <= $this->critical_level_sacks) {
            $criticalUnits[] = 'sacks';
        }
        
        if ($this->current_stock_pieces > 0 && $this->current_stock_pieces <= $this->critical_level_pieces) {
            $criticalUnits[] = 'pieces';
        }
        
        return $criticalUnits;
    }

    // Check if specific unit is critical
    public function isUnitCritical($unit)
    {
        switch ($unit) {
            case 'sack':
                return $this->current_stock_sacks > 0 && 
                       $this->current_stock_sacks <= $this->critical_level_sacks;
            case 'piece':
                return $this->current_stock_pieces > 0 && 
                       $this->current_stock_pieces <= $this->critical_level_pieces;
            default:
                return false;
        }
    }

    // Enhanced stock validation methods
    public function hasSufficientStock($unit, $quantity)
    {
        switch ($unit) {
            case 'sack':
                return $this->current_stock_sacks >= $quantity;
            case 'kilo':
                $sacksRequired = $quantity / 50; // Precise decimal calculation
                return $this->current_stock_sacks >= $sacksRequired;
            case 'piece':
                return $this->current_stock_pieces >= $quantity;
            default:
                return false;
        }
    }

    public function getAvailableStock($unit)
    {
        switch ($unit) {
            case 'sack':
                return $this->current_stock_sacks;
            case 'kilo':
                return $this->current_stock_sacks * 50;
            case 'piece':
                return $this->current_stock_pieces;
            default:
                return 0;
        }
    }

    public function getStockInfo($unit)
    {
        switch ($unit) {
            case 'sack':
                return "Available: {$this->current_stock_sacks} sacks";
            case 'kilo':
                $availableKilos = $this->current_stock_sacks * 50;
                return "Available: {$availableKilos} kilos ({$this->current_stock_sacks} sacks)";
            case 'piece':
                return "Available: {$this->current_stock_pieces} pieces";
            default:
                return "No stock available";
        }
    }

    public function calculateSacksRequired($unit, $quantity)
    {
        switch ($unit) {
            case 'sack':
                return $quantity;
            case 'kilo':
                return $quantity / 50; // Precise decimal calculation
            case 'piece':
                return 0; // Pieces don't require sacks
            default:
                return 0;
        }
    }

    public function calculatePrice($unit, $quantity)
    {
        switch ($unit) {
            case 'sack':
                return $this->price * $quantity;
            case 'kilo':
                $pricePerKilo = $this->price / 50;
                return $pricePerKilo * $quantity;
            case 'piece':
                // Assuming piece price is same as sack price for now
                // You might want to add a separate piece_price field
                return $this->price * $quantity;
            default:
                return 0;
        }
    }
}
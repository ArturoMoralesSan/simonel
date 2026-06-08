<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'date', 'inventory_id', 'product_id', 'sale_id', 'type', 'quantity', 'base_price'
    ];

    public function inventory() {
        return $this->belongsTo(Inventory::class);
    }

    public function sale() {
        return $this->belongsTo(Sale::class);
    }

    protected static function booted() {
        static::saved(function ($movement) {
            $movement->inventory->updateTotals();
        });
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerInventoryAssignmentLot extends Model
{
    protected $table = 'seller_inventory_assignment_lots';

    protected $fillable = [
        'assignment_id',
        'product_lot_id',
        'quantity',
        'available_quantity',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'available_quantity' => 'decimal:2',
    ];

    public function assignment()
    {
        return $this->belongsTo(
            SellerInventoryAssignment::class,
            'assignment_id'
        );
    }

    public function productLot()
    {
        return $this->belongsTo(
            ProductLot::class,
            'product_lot_id'
        );
    }
}
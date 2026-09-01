<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerInventoryMovement extends Model
{
    protected $table = 'seller_inventory_movements';

    protected $fillable = [
        'assignment_id',
        'assignment_lot_id',
        'seller_id',
        'product_lot_id',
        'type',
        'quantity',
        'reference_type',
        'reference_id',
        'comment',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
    ];

    /**
     * Asignación del día.
     */
    public function assignment(): BelongsTo
    {
        return $this->belongsTo(
            SellerInventoryAssignment::class,
            'assignment_id'
        );
    }

    /**
     * Detalle del lote asignado.
     */
    public function assignmentLot(): BelongsTo
    {
        return $this->belongsTo(
            SellerInventoryAssignmentLot::class,
            'assignment_lot_id'
        );
    }

    /**
     * Vendedor.
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'seller_id'
        );
    }

    /**
     * Lote original de producto terminado.
     */
    public function productLot(): BelongsTo
    {
        return $this->belongsTo(
            ProductLot::class,
            'product_lot_id'
        );
    }
}
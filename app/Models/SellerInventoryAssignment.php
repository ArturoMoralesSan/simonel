<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SellerInventoryAssignment extends Model
{
    protected $table = 'seller_inventory_assignments';

    protected $fillable = [
        'seller_id',
        'assignment_date',
        'status',
        'assigned_by',
        'returned_at',
    ];

    protected $casts = [
        'assignment_date' => 'date',
        'returned_at' => 'datetime',
    ];

    /**
     * Vendedor al que se asignó el inventario.
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Usuario que realizó la asignación.
     */
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Productos/lotes asignados.
     */
    public function lots(): HasMany
    {
        return $this->hasMany(
            SellerInventoryAssignmentLot::class,
            'assignment_id'
        );
    }

    /**
     * Movimientos del inventario del vendedor.
     */
    public function movements(): HasMany
    {
        return $this->hasMany(
            SellerInventoryMovement::class,
            'assignment_id'
        );
    }
}
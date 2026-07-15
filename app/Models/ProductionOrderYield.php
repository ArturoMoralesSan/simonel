<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionOrderYield extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_order_id',
        'type',
        'quantity',
        'unit',
        'notes',
    ];

    public function productionOrder()
    {
        return $this->belongsTo(ProductionOrder::class, 'production_order_id');
    }
}

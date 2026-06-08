<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionOrderItem extends Model
{
    use HasFactory;

    public function ProductionOrder()
    {
        return $this->belongsTo(ProductionOrder::class);
    }

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }
}

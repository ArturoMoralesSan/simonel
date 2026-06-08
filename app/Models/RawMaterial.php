<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawMaterial extends Model
{
    use HasFactory;

    public function lots()
    {
        return $this->hasMany(RawMaterialLot::class);
    }

    public function ProductionOrderItems()
    {
        return $this->hasMany(ProductionOrderItem::class);
    }
}

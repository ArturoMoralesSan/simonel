<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RawMaterial extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    public function lots()
    {
        return $this->hasMany(RawMaterialLot::class);
    }

    public function ProductionOrderItems()
    {
        return $this->hasMany(ProductionOrderItem::class);
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }
}

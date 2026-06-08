<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    public function lots()
    {
        return $this->hasMany(RawMaterialLot::class);
    }

    public function productLots()
    {
        return $this->hasMany(ProductLot::class);
    }
}

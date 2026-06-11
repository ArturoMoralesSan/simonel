<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleProductLot extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_product_id',
        'product_lot_id',
        'quantity',
    ];

    public function saleProduct()
    {
        return $this->belongsTo(SaleProduct::class, 'sale_product_id');
    }

    public function productLot()
    {
        return $this->belongsTo(ProductLot::class, 'product_lot_id'
        );
    }
}

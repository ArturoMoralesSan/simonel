<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleProduct extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity',
        'base_price',
        'subtotal',
        'discount',
        'iva',
        'total_with_iva',
        'total_cost',
        'profit'
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

     public function lots()
    {
        return $this->hasMany(SaleProductLot::class, 'sale_product_id');
    }

}

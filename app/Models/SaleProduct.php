<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleProduct extends Model
{
    use HasFactory;
    use SoftDeletes;
    
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
        return $this->belongsTo(Sale::class, 'sale_id')->withTrashed();
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id')->withTrashed();
    }

     public function lots()
    {
        return $this->hasMany(SaleProductLot::class, 'sale_product_id');
    }

}

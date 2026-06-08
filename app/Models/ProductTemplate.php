<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductTemplate extends Model
{
    use HasFactory;

    protected $appends = ['cut_name'];

    public function getCutNameAttribute()
    {
        return isset($this->cut_id) ? Cut::where('id', $this->cut_id)->value('name') : null;
    }
    
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function cut()
    {
        return $this->belongsTo(Cut::class);
    }

    public function sales()
    {
        return $this->belongsToMany(Sale::class, 'sale_products')
            ->withPivot(['product_name','quantity','base_price','subtotal', 'discount', 'iva', 'total_with_iva'])
            ->withTimestamps();
    }
}


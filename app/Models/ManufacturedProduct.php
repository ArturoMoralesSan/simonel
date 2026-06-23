<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManufacturedProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'description'
    ];

    public function recipes()
    {
        return $this->hasMany(ProductRecipe::class);
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'manufactured_product_id');
    }
}

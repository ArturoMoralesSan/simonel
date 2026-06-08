<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductRecipe extends Model
{
    use HasFactory;

    public function items()
    {
        return $this->hasMany(ProductRecipeItem::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

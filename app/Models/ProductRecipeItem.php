<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductRecipeItem extends Model
{
    use HasFactory;

    public function recipe()
    {
        return $this->belongsTo(ProductRecipe::class, 'product_recipe_id');
    }

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }
}

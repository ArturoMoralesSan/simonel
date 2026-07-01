<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */


    /**
     * Get the links that belong to the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    public function lots()
    {
        return $this->hasMany(ProductLot::class);
    }

    public function saleProducts()
    {
        return $this->hasMany(SaleProduct::class, 'product_id');
    }

    public function recipes()
    {
        return $this->hasMany(ProductRecipe::class, 'product_id');
    }

    public function manufactured()
    {
        return $this->belongsTo(ManufacturedProduct::class, 'manufactured_product_id')->withTrashed();
    }
}


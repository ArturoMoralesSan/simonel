<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */


    /**
     * Get the section that owns the types.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    /**
     * Get the section that owns the measure.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cut()
    {
        return $this->belongsTo(Cut::class);
    }


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
}


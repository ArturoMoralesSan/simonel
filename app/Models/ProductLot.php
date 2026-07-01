<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductLot extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'production_order_id',
        'warehouse_id',
        'lot_number',
        'barcode',
        'qr_code',
        'production_date',
        'expiration_date',
        'initial_quantity',
        'available_quantity',
        'cost_per_unit',
        'total_cost',
        'label_printed',
        'status',
        'active'
    ];

    protected $casts = [
        'production_date' => 'date',
        'expiration_date' => 'date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function order()
    {
        return $this->belongsTo(ProductionOrder::class, 'production_order_id')->withTrashed();
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class)->withTrashed();
    }
}
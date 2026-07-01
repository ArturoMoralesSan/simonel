<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionOrder extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $appends = [
        'formated_issue_date',
        'formated_delivery_date'
    ];

    public function getFormatedIssueDateAttribute()
    {
        return Carbon::parse($this->issue_date)
            ->format('d/m/Y');
    }

    public function getFormatedDeliveryDateAttribute()
    {
        return Carbon::parse($this->delivery_date)
            ->format('d/m/Y');
    }
    public function products()
    {
        return $this->hasMany(ProductionOrderProduct::class);
    }

    public function authorizer()
    {
        return $this->belongsTo(User::class, 'authorized_by')->withTrashed();
    }

    public function items()
    {
        return $this->hasMany(ProductionOrderItem::class, 'production_order_id');
    }

    public function productLot()
    {
        return $this->hasMany(ProductionLot::class, 'production_order_id');
    }
}

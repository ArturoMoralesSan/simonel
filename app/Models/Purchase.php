<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use HasFactory;
    use softDeletes;

    protected $fillable = [
        'supplier_id',
        'purchase_date',
        'invoice_number',
        'total',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'total' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class)->withTrashed();
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function lots()
    {
        return $this->hasMany(RawMaterialLot::class);
    }
}
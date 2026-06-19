<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class RawMaterialLot extends Model
{
    use HasFactory;

    protected $fillable = [
        'raw_material_id',
        'warehouse_id',
        'lot_number',
        'supplier_id',
        'supplier_lot',
        'purchase_id',
        'entry_date',
        'expiration_date',
        'initial_quantity',
        'available_quantity',
        'cost',
        'status',
    ];

    protected $appends = [
        'formated_entry_date',
        'formated_expiration_date'
    ];

    public function getFormatedEntryDateAttribute()
    {
        return Carbon::parse($this->entry_date)
            ->format('d/m/Y');
    }

    public function getFormatedExpirationDateAttribute()
    {
        return Carbon::parse($this->expiration_date)
            ->format('d/m/Y');
    }

    public function material()
    {
        return $this->belongsTo(RawMaterial::class, 'raw_material_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}

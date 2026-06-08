<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawMaterialMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'raw_material_id',
        'raw_material_lot_id',
        'movement_type',
        'quantity',
        'reference_type',
        'reference_id',
        'notes',
        'created_by'
    ];
    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }

    public function lot()
    {
        return $this->belongsTo(RawMaterialLot::class, 'raw_material_lot_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

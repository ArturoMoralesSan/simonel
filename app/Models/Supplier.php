<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $fillable = [
        'business_name',
        'trade_name',
        'contact_name',
        'rfc',
        'phone',
        'email',
        'address',
        'notes',
    ];

    
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function lots()
    {
        return $this->hasMany(RawMaterialLot::class);
    }
}

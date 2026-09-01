<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseBoning extends Model
{
    protected $table = 'purchase_boning';

    protected $fillable = [
        'purchase_id',

        'pierna',
        'pulpa',
        'hueso',
        'lonja',
        'cuero_planchar',
        'chamorro',

        'costilla',
        'piernas',
        'paleta',
        'lomo',
        'espinazo',
    ];

    protected $casts = [
        'pierna' => 'decimal:2',
        'pulpa' => 'decimal:2',
        'hueso' => 'decimal:2',
        'lonja' => 'decimal:2',
        'cuero_planchar' => 'decimal:2',
        'chamorro' => 'decimal:2',

        'costilla' => 'decimal:2',
        'piernas' => 'decimal:2',
        'paleta' => 'decimal:2',
        'lomo' => 'decimal:2',
        'espinazo' => 'decimal:2',
    ];

    public function purchase()
    {
        return $this->belongsTo(
            Purchase::class,
            'purchase_id'
        );
    }
}
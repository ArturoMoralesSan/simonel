<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    /**
     * A permission can be applied to many studies.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function sales()
    {
        return $this->belongsToMany(Sale::class, 'payment_sale')
        ->withPivot('cost')
        ->withTimestamps();
    }
}

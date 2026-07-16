<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function accountsReceivable()
    {
        return $this->hasMany(AccountReceivable::class, 'customer_id');
    }

    public function creditSetting()
    {
        return $this->hasOne(CustomerCreditSetting::class);
    }

    public function creditAuthorizations()
    {
        return $this->hasMany(CustomerCreditAuthorization::class);
    }

    public function seller()
    {
        return $this->belongsTo(
            User::class,
            'seller_id'
        );
    }
}

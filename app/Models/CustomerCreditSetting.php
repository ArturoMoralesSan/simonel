<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerCreditSetting extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'enabled',
        'credit_limit',
        'credit_days',
        'block_on_debt',
        'allow_over_limit',
        'notes',
        'authorized_by',
        'authorized_at'
    ];


    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function authorizedBy()
    {
        return $this->belongsTo(User::class, 'authorized_by');
    }
}

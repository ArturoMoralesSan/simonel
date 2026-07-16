<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerCreditAuthorization extends Model
{
    use HasFactory;


    protected $fillable = [
        'customer_id',
        'authorized_by',
        'reason',
        'expires_at',
    ];


    protected $casts = [
        'expires_at' => 'datetime',
    ];


    public function customer()
    {
        return $this->belongsTo(
            Customer::class
        );
    }


    public function authorizer()
    {
        return $this->belongsTo(
            User::class,
            'authorized_by'
        );
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountReceivablePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_receivable_id',
        'payment_id',
        'payment_date',
        'amount',
        'reference',
        'notes',
        'received_by'

    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function account()
    {
        return $this->belongsTo(AccountReceivable::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function receiver()
    {
        return $this->belongsTo(User::class,'received_by');
    }
}

<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class AccountReceivable extends Model
{

    protected $fillable = [
        'sale_id',
        'customer_id',
        'seller_id',
        'payment_id',
        'issue_date',
        'due_date',
        'original_amount',
        'paid_amount',
        'balance',
        'status',
        'notes',
    ];

    protected $casts = [
        'issue_date'      => 'date',
        'due_date'        => 'date',
        'original_amount' => 'decimal:2',
        'paid_amount'     => 'decimal:2',
        'balance'         => 'decimal:2',
    ];

    protected $appends = [
        'formated_issue_date',
        'formated_due_date'
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function payments()
    {
        return $this->hasMany(AccountReceivablePayment::class);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getFormatedIssueDateAttribute()
    {
        return $this->issue_date?->format('d/m/Y');
    }

    public function getFormatedDueDateAttribute()
    {
        return $this->due_date?->format('d/m/Y');
    }

    public function getIsOverdueAttribute()
    {
        return $this->balance > 0 &&
            $this->due_date &&
            $this->due_date->isPast();
    }

    public function getDaysOverdueAttribute()
    {
        if (!$this->is_overdue) {
            return 0;
        }

        return $this->due_date->diffInDays(now());
    }

    public function getPercentPaidAttribute()
    {
        if ($this->original_amount <= 0) {
            return 0;
        }

        return round(
            ($this->paid_amount / $this->original_amount) * 100,
            2
        );
    }
}
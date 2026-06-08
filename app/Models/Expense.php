<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Expense extends Model
{
    use HasFactory;

    protected $appends = ['formated_date'];

    /**
     * Return the slugified name of the section.
     *
     * @return string
     */
    public function getFormatedDateAttribute()
    {
        return Carbon::parse($this->date)->format('d/m/Y');
    }

    /**
     * Get the branch that owns the submenu.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the branch that owns the submenu.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type_expense()
    {
        return $this->belongsTo(TypeExpense::class);
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DashboardSubmenu extends Model
{
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['searchable_name'];


    /*
    |---------------------------------------------------------------------------
    | Accessors
    |---------------------------------------------------------------------------
    */

    /**
     * Return the text that will be used for filtering the item in dashboard menu.
     *
     * @return string
     */
    public function getSearchableNameAttribute()
    {
        return Str::slug($this->name, ' ');
    }


    /*
    |---------------------------------------------------------------------------
    | Relationships
    |---------------------------------------------------------------------------
    */

    /**
     * Get the links that belong to the submenu.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function links()
    {
        return $this->hasMany(DashboardLink::class, 'submenu_id');
    }


    /**
     * Get the section that owns the submenu.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function section()
    {
        return $this->belongsTo(DashboardSection::class);
    }
}

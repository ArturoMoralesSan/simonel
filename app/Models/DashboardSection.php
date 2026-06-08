<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DashboardSection extends Model
{
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['slug'];


    /*
    |---------------------------------------------------------------------------
    | Accessors
    |---------------------------------------------------------------------------
    */

    /**
     * Return the slugified name of the section.
     *
     * @return string
     */
    public function getSlugAttribute()
    {
        return Str::slug($this->name);
    }


    /*
    |---------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------.------------
    */

    /**
     * Get the submenus that belong to the section.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function submenus()
    {
        return $this->hasMany(DashboardSubmenu::class, 'section_id');
    }
}

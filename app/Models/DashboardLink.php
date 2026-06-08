<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DashboardLink extends Model
{
    /*
    |---------------------------------------------------------------------------
    | Relationships
    |---------------------------------------------------------------------------
    */

    /**
     * Get the permission associated to the link.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }


    /**
     * Get the submenu that owns the link.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function submenu()
    {
        return $this->belongsTo(DashboardSubmenu::class);
    }
}

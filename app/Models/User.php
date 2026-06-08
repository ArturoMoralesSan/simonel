<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','last_name','username', 'avatar', 'email', 'password', 'provider', 'provider_id', 'role_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    

    public function allPermissions()
    {
        return $this->role()->with('permissions')->get()->map(function ($item) {
            return $item->permissions->pluck('id');
        })->flatten();
    }

    /*
    |--------------------------------------------------------------------------
    | ACL
    |--------------------------------------------------------------------------
    */

    /**
     * Determine if the user may perform the given permission.
     *
     * @param  Permission $permission
     * @return boolean
     */
    public function hasPermission(Permission $permission)
    {
        return $this->hasRole($permission->roles);
    }

     /**
     * Determine if the user has the given role.
     *
     * @param  mixed $role
     * @return boolean
     */
    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->role->key_name == $role;
        }

        if ($role instanceof Role) {
            return $this->role->is($role);
        }

        return $role->contains($this->role);
    }

   /**
     * Determine if logged user has "superadmin" role.
     *
     * @return boolean
     */
    public function isSuperAdmin() {
        return $this->hasRole('superadmin');
    }

    public function isCustomer() {
        return $this->hasRole('customer');
    }

    public function isEmployee() {
        return $this->hasRole('employee');
    }


    

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the section that owns the role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the links that belong to the product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class, 'user_id');
    }

    public function customer()
    {
        return $this->hasOne(Customer::class);
    }

}

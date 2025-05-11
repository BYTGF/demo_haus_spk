<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Role;
use App\Models\Area;
use App\Models\Store;
use App\Models\InputFinance;
use App\Models\InputOperational;
use App\Models\InputBD;
use App\Models\InputStore;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        // 'name',
        'username',
        'password',
        'role_id',
        'area_id',
        'store_id',
        // 'phone',
        // 'location',
        // 'about_me',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function finances()
    {
        return $this->hasMany(InputFinance::class);
    }

    public function operationals()
    {
        return $this->hasMany(InputOperational::class);
    }

    public function stores()
    {
        return $this->hasMany(InputStore::class);
    }

    public function bds()
    {
        return $this->hasMany(InputBDS::class);
    }

    public function hasRole(...$roles)
    {
        return $this->role && in_array($this->role->role_name, $roles);
    }

    
}

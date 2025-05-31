<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Area;
use App\Models\User;
use App\Models\InputFinance;
use App\Models\InputOperational;
use App\Models\InputBD;
use App\Models\InputStore;

class Store extends Model
{
    use HasFactory;

    protected $fillable = ['area_id', 'store_name', 'is_active'];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function user()
    {
        return $this->hasMany(User::class);
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
        return $this->hasMany(InputBD::class);
    }
}

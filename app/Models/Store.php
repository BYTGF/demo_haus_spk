<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Area;
use App\Models\User;
use App\Models\InputFinance;

class Store extends Model
{
    use HasFactory;

    protected $fillable = ['area_id', 'store_name'];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function user()
    {
        return $this->hasMany(User::class);
    }
}

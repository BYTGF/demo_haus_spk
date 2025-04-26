<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Store;
use App\Models\User;

class Area extends Model
{
    use HasFactory;

    protected $fillable = ['area_name'];

    public function store()
    {
        return $this->hasMany(Store::class);
    }

    public function user()
    {
        return $this->hasMany(User::class);
    }
}

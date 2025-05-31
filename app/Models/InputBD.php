<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Store;

class InputBD extends Model
{
    use HasFactory;

    protected $casts = [
        'period' => 'date',
    ];


    protected $fillable = [
        'period','direct_competition', 'substitute_competition', 'indirect_competition',
        'rating', 'is_active','comment_input', 'comment_review', 'status',
        'user_id', 'store_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}

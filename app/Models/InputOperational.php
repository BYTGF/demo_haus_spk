<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Store;

class InputOperational extends Model
{
    use HasFactory;

    protected $casts = [
        'period' => 'date',
    ];

    protected $fillable = [
        'period', 'gaji_upah', 'sewa', 'utilitas', 'perlengkapan',
        'lain_lain', 'total', 'is_active','comment_input', 'comment_review',
        'status', 'user_id', 'store_id'
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Store;

class InputStore extends Model
{
    use HasFactory;

    protected $casts = [
        'period' => 'date',
        'lingkungan' => 'array',
    ];

    protected $fillable = [
        'period', 'aksesibilitas', 'visibilitas', 'lingkungan', 'lalu_lintas',
        'area_parkir', 'comment_input', 'comment_review','is_active',
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

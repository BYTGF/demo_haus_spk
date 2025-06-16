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
        'period',
        'aksesibilitas',
        'visibilitas', 
        'lingkungan',
        'lalu_lintas',
        'kepadatan_kendaraan', // Pastikan ini ada
        'parkir_mobil',       // Pastikan ini ada
        'parkir_motor',       // Pastikan ini ada
        'comment_input',
        'user_id',
        'status',
        'store_id'
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

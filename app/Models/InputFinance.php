<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Store;

class InputFinance extends Model
{
    use HasFactory;

    protected $casts = [
        'period' => 'date',
    ];
    
    protected $fillable = [
        'period', 'penjualan', 'pendapatan_lain', 'total_pendapatan',
        'total_hpp', 'laba_kotor', 'biaya_operasional', 'laba_sebelum_pajak',
        'laba_bersih', 'gross_profit_margin', 'net_profit_margin', 'rating','is_active',
        'comment_input', 'comment_review', 'status', 'user_id', 'store_id'
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

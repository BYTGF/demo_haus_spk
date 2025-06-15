<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\InputFinance;
use App\Models\User;
use App\Models\Store;
use Carbon\Carbon;



class InputFinanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $userIds = User::pluck('id')->toArray();
        $stores = Store::all();
        
        $statuses = ['Sedang Direview', 'Butuh Revisi', 'Selesai'];
        
        foreach ($stores as $store) {
            // Create 3-6 months of data for each store
            $months = rand(3, 6);
            
            for ($i = 0; $i < $months; $i++) {
                $period = Carbon::now()->subMonths($i);
                
                $penjualan = rand(40000000, 70000000);
                $pendapatan_lain = rand(500000, 2000000);
                $total_pendapatan = $penjualan + $pendapatan_lain;
                $total_hpp = rand(18000000, 25000000);
                $laba_kotor = $total_pendapatan - $total_hpp;
                $biaya_operasional = rand(30000000, 36000000);
                $laba_sebelum_pajak = $laba_kotor - $biaya_operasional;
                $pajak = abs(round($laba_sebelum_pajak * 0.1)); // Assume 10% tax
                $laba_bersih = $laba_sebelum_pajak - $pajak;
                
                $gross_margin = round(($laba_kotor / $total_pendapatan) * 100);
                $net_margin = round(($laba_bersih / $total_pendapatan) * 100);
                
                InputFinance::create([
                    'period' => $period,
                    'penjualan' => $penjualan,
                    'pendapatan_lain' => $pendapatan_lain,
                    'total_pendapatan' => $total_pendapatan,
                    'total_hpp' => $total_hpp,
                    'laba_kotor' => $laba_kotor,
                    'biaya_operasional' => $biaya_operasional,
                    'laba_sebelum_pajak' => $laba_sebelum_pajak, // Added this field
                    'laba_bersih' => $laba_bersih,
                    'gross_profit_margin' => $gross_margin,
                    'net_profit_margin' => $net_margin,
                    'rating' => rand(1, 5),
                    'comment_input' => 'Finance input for ' . $store->store_code,
                    'comment_review' => rand(0, 1) ? 'Finance review for ' . $store->store_code : null,
                    'is_active' => true,
                    'status' => $statuses[array_rand($statuses)],
                    'user_id' => $userIds[array_rand($userIds)],
                    'store_id' => $store->id,
                ]);
            }
        }
    }
}

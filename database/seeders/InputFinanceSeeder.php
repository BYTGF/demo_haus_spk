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
        $users = User::where('role_id', 6)->pluck('store_id', 'id')->toArray();
        $stores = Store::all();
        
        $statuses = ['Sedang Direview', 'Butuh Revisi', 'Selesai'];
        
        foreach ($users as $userId => $storeId) {
            // Create 3-6 months of data for each store
            $months = rand(3, 6);
            
            for ($i = 0; $i < $months; $i++) {
                $period = Carbon::now()->subMonths($i);

                $exists = InputFinance::where('store_id', $storeId)
                    ->where('user_id', $userId)
                    ->where('period', $period)
                    ->exists();

                if ($exists) {
                    continue; // Lewati jika sudah ada
                }
                
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
                
                InputFinance::updateOrCreate([
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
                    'comment_input' => 'Finance input for ' . $storeId,
                    'comment_review' => rand(0, 1) ? 'Finance review for ' . $storeId: null,
                    'is_active' => true,
                    'status' => $statuses[array_rand($statuses)],
                    'user_id' => $userId, // Pastikan user_id sesuai dengan store_id
                    'store_id' => $storeId, 
                ]);
            }
        }
    }
}

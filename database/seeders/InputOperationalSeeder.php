<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\InputOperational;
use App\Models\Store;
use Carbon\Carbon;

class InputOperationalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $users = User::where('role_id', 5)->pluck('store_id', 'id')->toArray();
        $stores = Store::all();
        
        $statuses = ['Sedang Direview', 'Butuh Revisi', 'Selesai'];
        
        foreach ($users as $userId => $storeId) {
            // Create 3-6 months of data for each store
            $months = rand(6, 12);
            
            for ($i = 0; $i < $months; $i++) {
                $period = Carbon::now()->subMonths($i);

                $exists = InputOperational::where('store_id', $storeId)
                    ->where('user_id', $userId)
                    ->where('period', $period)
                    ->exists();

                if ($exists) {
                    continue; // Lewati jika sudah ada
                }
                
                $gaji_upah = rand(10000000, 15000000);
                $sewa = rand(8000000, 11000000);
                $utilitas = rand(1500000, 3000000);
                $perlengkapan = rand(2000000, 3500000);
                $lain_lain = rand(3000000, 4500000);
                $total = $gaji_upah + $sewa + $utilitas + $perlengkapan + $lain_lain;
                
                InputOperational::updateOrCreate([
                    'period' => $period,
                    'gaji_upah' => $gaji_upah,
                    'sewa' => $sewa,
                    'utilitas' => $utilitas,
                    'perlengkapan' => $perlengkapan,
                    'lain_lain' => $lain_lain,
                    'total' => $total,
                    'comment_input' => 'Operational input for ' . $storeId,
                    'comment_review' => rand(0, 1) ? 'Operational review for ' . $storeId : null,
                    'is_active' => true,
                    'status' => 'Selesai',
                    'user_id' => $userId, // Pastikan user_id sesuai dengan store_id
                    'store_id' => $storeId, 
                ]);
            }
        }
    }
}

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
        $userIds = User::pluck('id')->toArray();
        $stores = Store::all();
        
        $statuses = ['Sedang Direview', 'Butuh Revisi', 'Selesai'];
        
        foreach ($stores as $store) {
            // Create 3-6 months of data for each store
            $months = rand(3, 6);
            
            for ($i = 0; $i < $months; $i++) {
                $period = Carbon::now()->subMonths($i);
                
                $gaji_upah = rand(10000000, 15000000);
                $sewa = rand(8000000, 11000000);
                $utilitas = rand(1500000, 3000000);
                $perlengkapan = rand(2000000, 3500000);
                $lain_lain = rand(3000000, 4500000);
                $total = $gaji_upah + $sewa + $utilitas + $perlengkapan + $lain_lain;
                
                InputOperational::create([
                    'period' => $period,
                    'gaji_upah' => $gaji_upah,
                    'sewa' => $sewa,
                    'utilitas' => $utilitas,
                    'perlengkapan' => $perlengkapan,
                    'lain_lain' => $lain_lain,
                    'total' => $total,
                    'comment_input' => 'Operational input for ' . $store->store_code,
                    'comment_review' => rand(0, 1) ? 'Operational review for ' . $store->store_code : null,
                    'is_active' => true,
                    'status' => $statuses[array_rand($statuses)],
                    'user_id' => $userIds[array_rand($userIds)],
                    'store_id' => $store->id,
                ]);
            }
        }
    }
}

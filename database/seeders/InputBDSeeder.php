<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\InputBD;
use App\Models\Store;
use Carbon\Carbon;

class InputBDSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run() 
    {
        $users = User::where('role_id', 4)->pluck('store_id', 'id')->toArray();
        $stores = Store::all();
        $statuses = ['Sedang Direview', 'Butuh Revisi', 'Selesai'];
        
        foreach ($users as $userId => $storeId) {
            // Create 3-6 months of data for each store
            $months = rand(3, 6);
            
            for ($i = 0; $i < $months; $i++) {
                // Gunakan awal bulan (tanggal 1) untuk konsistensi
                $period = Carbon::now()->subMonths($i)->startOfMonth();
                
                // Cek apakah data sudah ada dengan kombinasi user_id, store_id, dan period
                $exists = InputBD::where('store_id', $storeId)
                    ->where('user_id', $userId)
                    ->where('period', $period->format('Y-m-d'))
                    ->exists();
                    
                if ($exists) {
                    continue; // Lewati jika sudah ada
                }
                
                // Buat data baru
                InputBD::create([
                    'period' => $period,
                    'direct_competition' => rand(1, 5),
                    'substitute_competition' => rand(1, 5),
                    'indirect_competition' => rand(1, 5),
                    'comment_input' => 'BD input for store ' . $storeId,
                    'comment_review' => rand(0, 1) ? 'BD review for store ' . $storeId : null,
                    'is_active' => true,
                    'status' => $statuses[array_rand($statuses)],
                    'user_id' => $userId,
                    'store_id' => $storeId,
                ]);
            }
        }
    }
}

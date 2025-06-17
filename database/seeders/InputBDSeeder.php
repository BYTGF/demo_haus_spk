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
                $period = Carbon::now()->subMonths($i);

                $exists = InputBD::where('store_id', $storeId)
                    ->where('user_id', $userId)
                    ->where('period', $period)
                    ->exists();

                if ($exists) {
                    continue; // Lewati jika sudah ada
                }
                
                InputBD::updateOrCreate([
                    'period' => $period,
                    'direct_competition' => rand(1, 5),
                    'substitute_competition' => rand(1, 5),
                    'indirect_competition' => rand(1, 5),
                    'comment_input' => 'BD input for ' . $storeId,
                    'comment_review' => rand(0, 1) ? 'BD review for ' . $storeId : null,
                    'is_active' => true,
                    'status' => $statuses[array_rand($statuses)],
                    'user_id' => $userId, // Pastikan user_id sesuai dengan store_id
                    'store_id' => $storeId, 
                ]);
            }
        }
    }
}

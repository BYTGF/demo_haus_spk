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
        $userIds = User::pluck('id')->toArray();
        $stores = Store::all();
        
        $statuses = ['Sedang Direview', 'Butuh Revisi', 'Selesai'];
        
        foreach ($stores as $store) {
            // Create 3-6 months of data for each store
            $months = rand(3, 6);
            
            for ($i = 0; $i < $months; $i++) {
                $period = Carbon::now()->subMonths($i);
                
                InputBD::create([
                    'period' => $period,
                    'direct_competition' => rand(1, 5),
                    'substitute_competition' => rand(1, 5),
                    'indirect_competition' => rand(1, 5),
                    'rating' => rand(1, 5),
                    'comment_input' => 'BD input for ' . $store->store_code,
                    'comment_review' => rand(0, 1) ? 'BD review for ' . $store->store_code : null,
                    'is_active' => true,
                    'status' => $statuses[array_rand($statuses)],
                    'user_id' => $userIds[array_rand($userIds)],
                    'store_id' => $store->id,
                ]);
            }
        }
    }
}

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
        // Get all Business Development users (role_id 4)
        $bdUsers = User::where('role_id', 4)->get();
        
        // Get all active stores (excluding the default store if needed)
        $stores = Store::where('is_active', true)
                    ->where('id', '!=', 1) // Exclude default store if needed
                    ->get();
        
        $statuses = ['Sedang Direview', 'Butuh Revisi', 'Selesai'];
        
        // If there are no stores or no BD users, return
        if ($stores->isEmpty() || $bdUsers->isEmpty()) {
            return;
        }
        
        foreach ($stores as $store) {
            // Randomly select a BD user for this store
            $randomUser = $bdUsers->random();
            
            // Create 6-12 months of data for each store
            $months = rand(6, 12);
            
            for ($i = 0; $i < $months; $i++) {
                // Use start of month (1st day) for consistency
                $period = Carbon::now()->subMonths($i)->startOfMonth();
                
                // Check if data already exists for this store and period
                $exists = InputBD::where('store_id', $store->id)
                            ->where('period', $period->format('Y-m-d'))
                            ->exists();
                            
                if ($exists) {
                    continue; // Skip if already exists
                }
                
                // Create new data
                InputBD::create([
                    'period' => $period,
                    'direct_competition' => rand(1, 5),
                    'substitute_competition' => rand(1, 5),
                    'indirect_competition' => rand(1, 5),
                    'comment_input' => 'BD input for store ' . $store->id,
                    'comment_review' => rand(0, 1) ? 'BD review for store ' . $store->id : null,
                    'is_active' => true,
                    'status' => 'Selesai', // Random status
                    'user_id' => $randomUser->id,
                    'store_id' => $store->id,
                ]);
            }
        }
    }
}

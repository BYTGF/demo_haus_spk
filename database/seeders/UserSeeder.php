<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Store;
use App\Models\Area;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
   public function run()
    {
        $roles = Role::pluck('id', 'role_name')->toArray();
        $areaIds = Area::pluck('id')->toArray();
        $storeIds = Store::pluck('id')->toArray();
        // Create admin user (Head Office)
        User::create([
            'username' => 'admin',
            'password' => Hash::make('password'),
            'is_active' => true,
            'role_id' => $roles['Admin'],
            'area_id' => 1,
            'store_id' => 1,
        ]);

        // Create C-Level users (2-3 executives) - Head Office
        for ($i = 1; $i <= rand(2, 3); $i++) {
            User::create([
                'username' => 'ceo_' . $i,
                'password' => Hash::make('password'),
                'is_active' => true,
                'role_id' => $roles['C-Level'],
                'area_id' => 1,
                'store_id' => 1,
            ]);
        }

        // Create Business Development Team - Head Office
        User::create([
            'username' => 'bd_manager',
            'password' => Hash::make('password'),
            'is_active' => true,
            'role_id' => $roles['Manager Business Development'],
            'area_id' => 1,
            'store_id' => 1,
        ]);

        // Create 3-5 BD Staff - Head Office
        for ($i = 1; $i <= rand(3, 5); $i++) {
            User::create([
                'username' => 'bd_staff_' . $i,
                'password' => Hash::make('password'),
                'is_active' => true,
                'role_id' => $roles['Business Development Staff'],
                'area_id' => 1,
                'store_id' => 1,
            ]);
        }

        // Create Operational users (3-5) - Pastikan store sesuai area
        for ($i = 1; $i <= rand(3, 5); $i++) {
            $areaId = $areaIds[array_rand($areaIds)];
            $store = Store::where('area_id', $areaId)->inRandomOrder()->first();
            
            User::create([
                'username' => 'operational_' . $i,
                'password' => Hash::make('password'),
                'is_active' => true,
                'role_id' => $roles['Operational'],
                'area_id' => $areaId,
                'store_id' => $store->id,
            ]);
        }

        // Create Finance users (2-3) - Pastikan store sesuai area
        for ($i = 1; $i <= rand(2, 3); $i++) {
            $areaId = $areaIds[array_rand($areaIds)];
            $store = Store::where('area_id', $areaId)->inRandomOrder()->first();
            
            User::create([
                'username' => 'finance_' . $i,
                'password' => Hash::make('password'),
                'is_active' => true,
                'role_id' => $roles['Finance'],
                'area_id' => $areaId,
                'store_id' => $store->id,
            ]);
        }

        // Create Area Managers (one per area) - Store 1 di area masing-masing
        foreach ($areaIds as $areaId) {
            if ($areaId == 1) continue; // Skip Head Office
            
            $store = Store::where('area_id', $areaId)->first();
            if (!$store) {
                $store = Store::create([
                    'area_id' => $areaId,
                    'store_code' => 'AR-' . $areaId . '-001',
                    'store_name' => 'Main Store Area ' . $areaId,
                    'is_active' => true
                ]);
            }
            
            User::create([
                'username' => 'area_manager_' . $areaId,
                'password' => Hash::make('password'),
                'is_active' => true,
                'role_id' => $roles['Area Manager'],
                'area_id' => $areaId,
                'store_id' => $store->id,
            ]);
        }

        // Create Store Managers (one per store) - Pastikan sesuai area
        foreach (Store::all() as $store) {
            if ($store->id == 1) continue; // Skip Head Office store
            
            User::create([
                'username' => 'store_manager_' . $store->id,
                'password' => Hash::make('password'),
                'is_active' => true,
                'role_id' => $roles['Store Manager'],
                'area_id' => $store->area_id,
                'store_id' => $store->id,
            ]);
        }
    }
}

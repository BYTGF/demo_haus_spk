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
        
        // Create admin user
        User::create([
            'username' => 'admin',
            'password' => Hash::make('password'),
            'is_active' => true,
            'role_id' => $roles['Admin'],
            'area_id' => $areaIds[array_rand($areaIds)],
            'store_id' => $storeIds[array_rand($storeIds)],
        ]);

        // Create C-Level users (2-3 executives)
        for ($i = 1; $i <= rand(2, 3); $i++) {
            User::create([
                'username' => 'ceo_' . $i,
                'password' => Hash::make('password'),
                'is_active' => true,
                'role_id' => $roles['C-Level'],
                'area_id' => $areaIds[array_rand($areaIds)],
                'store_id' => $storeIds[array_rand($storeIds)],
            ]);
        }

        // Create Business Development Team
        User::create([
            'username' => 'bd_manager',
            'password' => Hash::make('password'),
            'is_active' => true,
            'role_id' => $roles['Manager Business Development'],
            'area_id' => $areaIds[array_rand($areaIds)],
            'store_id' => $storeIds[array_rand($storeIds)],
        ]);

        // Create 3-5 BD Staff
        for ($i = 1; $i <= rand(3, 5); $i++) {
            User::create([
                'username' => 'bd_staff_' . $i,
                'password' => Hash::make('password'),
                'is_active' => true,
                'role_id' => $roles['Business Development Staff'],
                'area_id' => $areaIds[array_rand($areaIds)],
                'store_id' => $storeIds[array_rand($storeIds)],
            ]);
        }

        // Create Operational users (3-5)
        for ($i = 1; $i <= rand(3, 5); $i++) {
            User::create([
                'username' => 'operational_' . $i,
                'password' => Hash::make('password'),
                'is_active' => true,
                'role_id' => $roles['Operational'],
                'area_id' => $areaIds[array_rand($areaIds)],
                'store_id' => $storeIds[array_rand($storeIds)],
            ]);
        }

        // Create Finance users (2-3)
        for ($i = 1; $i <= rand(2, 3); $i++) {
            User::create([
                'username' => 'finance_' . $i,
                'password' => Hash::make('password'),
                'is_active' => true,
                'role_id' => $roles['Finance'],
                'area_id' => $areaIds[array_rand($areaIds)],
                'store_id' => $storeIds[array_rand($storeIds)],
            ]);
        }

        // Create Area Managers (one per area)
        foreach ($areaIds as $areaId) {
            User::create([
                'username' => 'area_manager_' . $areaId,
                'password' => Hash::make('password'),
                'is_active' => true,
                'role_id' => $roles['Area Manager'],
                'area_id' => $areaId,
                'store_id' => Store::where('area_id', $areaId)->first()->id,
            ]);
        }

        // Create Store Managers (one per store)
        foreach ($storeIds as $storeId) {
            $store = Store::find($storeId);
            User::create([
                'username' => 'store_manager_' . $storeId,
                'password' => Hash::make('password'),
                'is_active' => true,
                'role_id' => $roles['Store Manager'],
                'area_id' => $store->area_id,
                'store_id' => $storeId,
            ]);
        }
    }
}

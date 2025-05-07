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
        $roles = Role::all();
        $areas = Area::all();
        $stores = Store::all();

        // contoh buat 1 user untuk setiap role
        foreach ($roles as $role) {
            // Determine area_id and store_id based on role_id conditions
            if ($role->id == 8 || $role->id == 5 || $role->id == 6) {
                // If role_id is 7, area_id and store_id are both set to 1
                $area_id = $areas->random()->id;
                $store_id = $stores->random()->id;
            } elseif ($role->id == 7) {
                // If role_id is 6, area_id and store_id are both random
                $area_id = $areas->random()->id;
                $store_id = 1;
            } else {
                // If role_id is neither 6 nor 7, area_id is set to 1 and store_id is random
                $area_id = 1;
                $store_id = 1;
            }

            // Create the user with the determined area_id and store_id
            User::create([
                'username' => strtolower($role->role_name),
                'password' => Hash::make('pass'), // default password "pass"
                'role_id' => $role->id,
                'area_id' => $area_id,
                'store_id' => $store_id,
            ]);
        }
    }
}

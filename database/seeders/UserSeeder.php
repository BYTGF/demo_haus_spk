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
            User::create([
                'username' => strtolower($role->role_name) . '_user',
                'password' => Hash::make('password'), // semua default password "password"
                'role_id' => $role->id,
                'area_id' => $areas->random()->id,
                'store_id' => $stores->random()->id,
            ]);
        }
    }
}

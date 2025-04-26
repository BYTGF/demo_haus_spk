<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ['Admin', 'C-Level', 'Manager Business Development', 'Business Development Staff', 'Operational', 'Finance', 'Area Manager', 'Store Manager'];

        foreach ($roles as $role) {
            Role::create([
                'role_name' => $role
            ]);
        }
    }
}

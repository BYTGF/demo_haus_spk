<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call([
            RoleSeeder::class,
            AreaSeeder::class,
            StoreSeeder::class,
            UserSeeder::class,
            InputFinanceSeeder::class,
            InputOperationalSeeder::class,
            InputStoreSeeder::class,
            InputBDSeeder::class,
            CriteriaWeightSeeder::class,
        ]);
    }
}

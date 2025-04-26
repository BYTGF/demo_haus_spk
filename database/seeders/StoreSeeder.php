<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Store;
use App\Models\Area;


class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $areas = Area::all();

        foreach ($areas as $area) {
            for ($i = 1; $i <= 2; $i++) { // setiap area punya 2 toko
                Store::create([
                    'area_id' => $area->id,
                    'store_name' => $area->area_name . ' Store ' . $i
                ]);
            }
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Area;


class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $areas = [['HO','Head Office'],['JKT','Jakarta'], ['BDG', 'Bandung'], ['BOG', 'BOGOR']];

        foreach ($areas as $area) {
            Area::create([
                'area_code' => $area[0],
                'area_name' => $area[1]
            ]);
        }
    }
}

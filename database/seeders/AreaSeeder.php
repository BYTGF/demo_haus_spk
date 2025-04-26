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
        $areas = ['Jakarta', 'Bandung', 'Surabaya'];

        foreach ($areas as $area) {
            Area::create([
                'area_name' => $area
            ]);
        }
    }
}

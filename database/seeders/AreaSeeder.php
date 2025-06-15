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
    public function run()
    {
        $areas = [
            ['area_code' => 'AREA-001', 'area_name' => 'Jakarta Pusat'],
            ['area_code' => 'AREA-002', 'area_name' => 'Jakarta Barat'],
            ['area_code' => 'AREA-003', 'area_name' => 'Jakarta Selatan'],
            ['area_code' => 'AREA-004', 'area_name' => 'Jakarta Timur'],
            ['area_code' => 'AREA-005', 'area_name' => 'Jakarta Utara'],
            ['area_code' => 'AREA-006', 'area_name' => 'Bogor'],
            ['area_code' => 'AREA-007', 'area_name' => 'Depok'],
            ['area_code' => 'AREA-008', 'area_name' => 'Tangerang'],
            ['area_code' => 'AREA-009', 'area_name' => 'Bekasi'],
            ['area_code' => 'AREA-010', 'area_name' => 'Bandung'],
        ];

        foreach ($areas as $area) {
            Area::create($area);
        }
    }
}

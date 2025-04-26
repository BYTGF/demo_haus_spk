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

        $areas = [['1','MNG','Manager'], ['2','TNB','Tanah Abang'], ['2','PLT','Pluit'], ['3','DGO','Dago'], ['3','PST','Pasteur'], ['4','CKP','Cikupa'], ['4','CAW','Ciawi']];

        foreach ($areas as $area) {
            Store::create([
                'area_id' => $area[0],
                'store_code' => $area[1],
                'store_name' => $area[2]
            ]);
            
        }
    }
}

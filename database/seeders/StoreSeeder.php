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
     public function run()
    {
        $areaIds = Area::pluck('id')->toArray();
        
        $stores = [];
        for ($i = 1; $i <= 40; $i++) {
            $storeCode = 'HAUS-' . str_pad($i, 3, '0', STR_PAD_LEFT);
            $stores[] = [
                'area_id' => $areaIds[array_rand($areaIds)],
                'store_code' => $storeCode,
                'store_name' => 'Store ' . $i,
                'is_active' => true,
            ];
        }

        foreach ($stores as $store) {
            Store::create($store);
        }
    }
}

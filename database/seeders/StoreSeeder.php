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

        // Store pertama khusus untuk Manager
        $stores[] = [
            'area_id' => $areaIds[array_rand($areaIds)], // Area tetap bisa dipilih secara acak
            'store_code' => 'HAUS-001',
            'store_name' => 'Manager Store',
            'is_active' => true,
        ];

        // Store berikutnya diacak
        for ($i = 2; $i <= 40; $i++) {
            $storeCode = 'HAUS-' . str_pad($i, 3, '0', STR_PAD_LEFT);
            $stores[] = [
                'area_id' => $areaIds[array_rand($areaIds)], // Area acak
                'store_code' => $storeCode,
                'store_name' => 'Store ' . $i, // Nama acak
                'is_active' => true,
            ];
        }

        // Simpan ke database
        foreach ($stores as $store) {
            Store::create($store);
        }

    }
}

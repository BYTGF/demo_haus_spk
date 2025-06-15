<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\InputStore;
use App\Models\User;
use App\Models\Store;
use Carbon\Carbon;

class InputStoreSeeder extends Seeder
{
    public function run()
    {
        $userIds = User::pluck('id')->toArray();
        $stores = Store::all();
        
        $statuses = ['Sedang Direview Manager Area', 'Sedang Direview Manager BD', 'Butuh Revisi', 'Selesai'];
        
        $environments = [
            ['Kampus'],
            ['Perumahan'],
            ['Sekolah'],
            ['Kampus', 'Perumahan'],
            ['Kampus', 'Sekolah'],
            ['Kampus', 'Perumahan', 'Sekolah'],
        ];
        
        foreach ($stores as $store) {
            // Create 3-6 months of data for each store
            $months = rand(3, 6);
            
            for ($i = 0; $i < $months; $i++) {
                $period = Carbon::now()->subMonths($i);
                
                $aksesibilitas = rand(1, 3);
                $visibilitas = rand(1, 2);
                $env = $environments[array_rand($environments)];
                $lalu_lintas = rand(1, 3);
                $kepadatan = rand(1, 3);
                $parkir_mobil = rand(1, 3);
                $parkir_motor = rand(1, 3);
                
                $total = $aksesibilitas + $visibilitas + count($env) + $lalu_lintas + $kepadatan + $parkir_mobil + $parkir_motor;
                
                InputStore::create([
                    'period' => $period,
                    'aksesibilitas' => $aksesibilitas,
                    'visibilitas' => $visibilitas,
                    'lingkungan' => json_encode($env),
                    'lalu_lintas' => $lalu_lintas,
                    'kepadatan_kendaraan' => $kepadatan,
                    'parkir_mobil' => $parkir_mobil,
                    'parkir_motor' => $parkir_motor,
                    'comment_input' => 'Input for ' . $store->store_code . ' on ' . $period->format('Y-m'),
                    'comment_review' => rand(0, 1) ? 'Review comment for ' . $store->store_code : null,
                    'is_active' => true,
                    'status' => $statuses[array_rand($statuses)],
                    'user_id' => $userIds[array_rand($userIds)],
                    'store_id' => $store->id,
                ]);
            }
        }
    }
}

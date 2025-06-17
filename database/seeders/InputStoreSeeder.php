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
        $users = User::where('role_id', 8)->pluck('store_id', 'id')->toArray();// Ambil user_id dan store_id
        $statuses = ['Sedang Direview Manager Area', 'Sedang Direview Manager BD', 'Butuh Revisi', 'Selesai'];
        
        $environments = [
            ['Kampus'],
            ['Perumahan'],
            ['Sekolah'],
            ['Kampus', 'Perumahan'],
            ['Kampus', 'Sekolah'],
            ['Kampus', 'Perumahan', 'Sekolah'],
        ];
        
        foreach ($users as $userId => $storeId) { // Loop berdasarkan user_id dan store_id
            $months = rand(3, 6); // Generate data untuk 3-6 bulan
            
            for ($i = 0; $i < $months; $i++) {
                $period = Carbon::now()->subMonths($i);

                $exists = InputStore::where('store_id', $storeId)
                    ->where('user_id', $userId)
                    ->where('period', $period)
                    ->exists();

                if ($exists) {
                    continue; // Lewati jika sudah ada
                }
                
                $aksesibilitas = rand(1, 3);
                $visibilitas = rand(1, 2);
                $env = $environments[array_rand($environments)];
                $lalu_lintas = rand(1, 3);
                $kepadatan = rand(1, 3);
                $parkir_mobil = rand(1, 3);
                $parkir_motor = rand(1, 3);
                
                InputStore::updateOrCreate([
                    'period' => $period,
                    'aksesibilitas' => $aksesibilitas,
                    'visibilitas' => $visibilitas,
                    'lingkungan' => json_encode($env),
                    'lalu_lintas' => $lalu_lintas,
                    'kepadatan_kendaraan' => $kepadatan,
                    'parkir_mobil' => $parkir_mobil,
                    'parkir_motor' => $parkir_motor,
                    'comment_input' => 'Input for store ID ' . $storeId . ' on ' . $period->format('Y-m'),
                    'comment_review' => rand(0, 1) ? 'Review comment for store ID ' . $storeId : null,
                    'is_active' => true,
                    'status' => $statuses[array_rand($statuses)],
                    'user_id' => $userId, // Pastikan user_id sesuai dengan store_id
                    'store_id' => $storeId, // Ambil store_id langsung dari User
                ]);
            }
        }
    }

}

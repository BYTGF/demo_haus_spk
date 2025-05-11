<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\InputStore;
use App\Models\User;

class InputStoreSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('store_id', '!=', 1)->get();

        foreach ($users as $user) {
            $usedPeriods = [];

            for ($i = 0; $i < 10; $i++) {
                // Generate period unik buat store ini
                do {
                    $period = now()->subMonths(rand(1, 12))->startOfMonth()->format('Y-m-d');
                } while (in_array($period, $usedPeriods));

                $usedPeriods[] = $period;

                // Cek apakah udah ada data untuk store + period itu
                $exists = InputStore::where('store_id', $user->store_id)
                    ->where('period', $period)
                    ->exists();

                if (!$exists) {
                    InputStore::create([
                        'period' => $period,
                        'aksesibilitas' => rand(1, 5),
                        'visibilitas' => rand(1, 5),
                        'lingkungan' => json_encode([
                            '1',
                            '2',
                            '3', // You can add more depending on your needs
                        ]),
                        'lalu_lintas' => rand(1, 5),
                        'kepadatan_kendaraan' => rand(1, 5),
                        'parkir_mobil' => rand(1, 5),
                        'parkir_motor' => rand(1, 5),
                        'rating' => rand(1, 5),
                        'comment_input' => 'Komentar input ke-' . ($i + 1),
                        'comment_review' => 'Komentar review ke-' . ($i + 1),
                        'status' => collect([
                            'Sedang Direview Manager Area',
                            'Sedang Direview Manager BD',
                            'Butuh Revisi',
                            'Selesai'
                        ])->random(),
                        'user_id' => $user->id,
                        'store_id' => $user->store_id,
                    ]);
                }
            }
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\InputOperational;

class InputOperationalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('store_id', '!=', 1)->get();;

        foreach ($users as $user) {
            for ($i = 0; $i < 10; $i++) {
                InputOperational::create([
                    'period' => now()->subMonths(rand(1, 12))->format('Y-m-d'),
                    'gaji_upah' => rand(3000, 15000),
                    'sewa' => rand(2000, 10000),
                    'utilitas' => rand(500, 5000),
                    'perlengkapan' => rand(500, 5000),
                    'lain_lain' => rand(1000, 7000),
                    'total' => rand(7000, 40000),
                    'rating' => rand(1, 5),
                    'comment_input' => 'Komentar input ke-' . ($i + 1),
                    'comment_review' => 'Komentar review ke-' . ($i + 1),
                    'status' => collect(['Selesai', 'Sedang Direview', 'Butuh Revisi'])->random(),
                    'user_id' => $user->id,
                    'store_id' => $user->store_id,
                ]);
            }
        }
    }
}

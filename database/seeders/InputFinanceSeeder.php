<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\InputFinance;



class InputFinanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 10; $i++) {
            InputFinance::create([
                'neraca_keuangan' => rand(1000, 5000),
                'arus_kas' => rand(500, 3000),
                'profitabilitas' => rand(10, 100),
                'comment_input' => 'Komentar input ke-' . ($i+1),
                'comment_review' => 'Komentar review ke-' . ($i+1),
                'status' => collect(['Selesai', 'Sedang Direview', 'Butuh Revisi'])->random(),
                'user_id' => 6, // Ganti sesuai user yang ada
                'store_id' => rand(1,7), // Ganti sesuai store yang ada
            ]);
        }
    }
}

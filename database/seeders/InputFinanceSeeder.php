<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\InputFinance;
use App\Models\User;



class InputFinanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('store_id', '!=', 1)->get();; // Get all users

        foreach ($users as $user) {
            for ($i = 0; $i < 10; $i++) {
                InputFinance::create([
                    'period' => now()->subMonths(rand(1, 12))->format('Y-m-d'),
                    'penjualan' => rand(10000, 50000),
                    'pendapatan_lain' => rand(2000, 10000),
                    'total_pendapatan' => rand(15000, 60000),
                    'total_hpp' => rand(5000, 30000),
                    'laba_kotor' => rand(5000, 30000),
                    'biaya_operasional' => rand(2000, 15000),
                    'laba_sebelum_pajak' => rand(3000, 20000),
                    'laba_bersih' => rand(2500, 18000),
                    'gross_profit_margin' => rand(20, 80),
                    'net_profit_margin' => rand(10, 50),
                    'rating' => rand(1, 5),
                    'comment_input' => 'Komentar input ke-' . ($i + 1),
                    'comment_review' => 'Komentar review ke-' . ($i + 1),
                    'status' => collect(['Selesai', 'Sedang Direview', 'Butuh Revisi'])->random(),
                    'user_id' => $user->id,
                    'store_id' => $user->store_id, // Ensuring same store_id as user
                ]);
            }
        }
    }
}

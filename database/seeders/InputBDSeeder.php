<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\InputBD;

class InputBDSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('store_id', '!=', 1)->get();;

        foreach ($users as $user) {
            for ($i = 0; $i < 10; $i++) {
                InputBD::create([
                    'period' => now()->subMonths(rand(1, 12))->format('Y-m-d'),
                    'direct_competition' => rand(1, 5),
                    'substitute_competition' => rand(1, 5),
                    'indirect_competition' => rand(1, 5),
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

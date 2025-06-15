<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CriteriaWeight;

class CriteriaWeightSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cws = [['finance',10],['operational',15], ['bd', 5], ['store', 8]];

        foreach ($cws as $cw) {
            CriteriaWeight::create([
                'criteria' => $cw[0],
                'weight' => $cw[1]
            ]);
        }
    }
}

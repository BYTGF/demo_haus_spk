<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StoreReviewController extends Controller
{
    public function index(Request $request)
{
    if (auth()->user()->role->role_name !== 'Manager Business Development' && auth()->user()->role->role_name !== 'C-Level') {
        abort(403, 'Akses ditolak.');
    }

    $periodChoice = $request->get('period', '6');
    $monthBack = (int) $periodChoice;

    try {
        // Ambil semua store aktif
        $allStores = Store::where('is_active', true)
            ->where('id', '!=', 1)
            ->get();

        // Ambil bobot kriteria
        $criteriaWeightsRaw = DB::table('criteria_weights')
            ->where('is_active', true)
            ->pluck('weight', 'criteria');

        $totalWeight = $criteriaWeightsRaw->sum();
        $criteriaWeights = [];
        foreach ($criteriaWeightsRaw as $criteria => $weight) {
            $criteriaWeights[$criteria] = $weight / $totalWeight;
        }

        // Hitung semua nilai mentah
        $financeData = [];
        $operationalData = [];
        $bdData = [];
        $storeData = [];

        foreach ($allStores as $store) {
            $finance = $store->finances()
                ->where('is_active', true)
                ->where('period', '>=', now()->subMonths($monthBack))
                ->sum('net_profit_margin');

            $operational = $store->operationals()
                ->where('is_active', true)
                ->where('period', '>=', now()->subMonths($monthBack))
                ->sum('total');

            $bdItems = $store->bds()
                ->where('is_active', true)
                ->where('period', '>=', now()->subMonths($monthBack))
                ->get();

            $bdScore = 0;
            foreach ($bdItems as $item) {
                $bdScore += ($item->direct_competition ?? 0) * 2;
                $bdScore += ($item->indirect_competition ?? 0) * 1;
                $bdScore += ($item->substitute_competition ?? 0) * 1;
            }

            $storeInput = $store->stores()
                ->where('is_active', true)
                ->where('period', '>=', now()->subMonths($monthBack))
                ->latest()
                ->first();

            $storeScore = 0;
            if ($storeInput) {
                $storeScore += (int) $storeInput->aksesibilitas;
                $storeScore += (int) $storeInput->visibilitas;
                $storeScore += (int) $storeInput->lingkungan;
                $storeScore += (int) $storeInput->lalu_lintas;
                $storeScore += (int) $storeInput->area_parkir;
            }

            $financeData[] = $finance;
            $operationalData[] = $operational;
            $bdData[] = $bdScore;
            $storeData[] = $storeScore;

            $store->raw_scores = [
                'finance'     => $finance,
                'operational' => $operational,
                'bd'          => $bdScore,
                'store'       => $storeScore,
            ];
        }

        // Hitung min & max untuk normalisasi
        $minMax = [
            'finance'     => ['min' => min($financeData), 'max' => max($financeData)],
            'operational' => ['min' => min($operationalData), 'max' => max($operationalData)],
            'bd'          => ['min' => min($bdData), 'max' => max($bdData)],
            'store'       => ['min' => min($storeData), 'max' => max($storeData)],
        ];

        // Hitung skor final
        $scoredStores = $allStores->map(function ($store) use ($criteriaWeights, $minMax) {
            $normalized = [];

            foreach ($store->raw_scores as $key => $value) {
                $min = $minMax[$key]['min'];
                $max = $minMax[$key]['max'];
                $normalized[$key] = $max !== $min ? ($value - $min) / ($max - $min) : 0;
            }

            $finalScore = 0;
            foreach ($normalized as $key => $value) {
                $finalScore += $value * ($criteriaWeights[$key] ?? 0);
            }

            $store->final_score = round($finalScore * 100, 0);
            $store->status = $store->final_score < 50 ? 'Layak Tutup' : 'Layak Buka';

            return $store;
        });

        // Urutkan dari yang skor paling kecil
        $sortedStores = $scoredStores->sortBy('final_score')->values();

        // Paginate manual
        $page = $request->get('page', 1);
        $perPage = 5;
        $paginatedStores = new \Illuminate\Pagination\LengthAwarePaginator(
            $sortedStores->forPage($page, $perPage),
            $sortedStores->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $closed = Store::with('area')->where('is_active', false)->paginate(10);

        return view('review-store', compact('paginatedStores', 'periodChoice', 'closed'));
    } catch (\Throwable $e) {
        Log::error('StoreReview@index error: ' . $e->getMessage());
        return back()->with('error', 'Gagal memuat data.');
    }
}



    public function update(Request $request, Store $store)
    {
        if (auth()->user()->role->role_name !== 'C-Level') {
            abort(403, 'Akses ditolak.');
        }

        try {
            DB::transaction(function () use ($store) {
                $store->update(['is_active' => false]);
                $store->finances()->update(['is_active' => false]);
                $store->operationals()->update(['is_active' => false]);
                $store->bds()->update(['is_active' => false]);
                $store->stores()->update(['is_active' => false]);
            });

            return redirect()->route('review-store.index')
                ->with('success', 'Store dan semua data terkait berhasil dinonaktifkan.');
        } catch (\Throwable $e) {
            Log::error('StoreReview@update error: ' . $e->getMessage());
            return back()->with('error', 'Gagal menonaktifkan store.');
        }
    }
}

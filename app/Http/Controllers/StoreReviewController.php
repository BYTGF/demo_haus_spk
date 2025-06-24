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
            // Get all active stores
            $allStores = Store::where('is_active', true)
                ->where('id', '!=', 1)
                ->get();

            // Get criteria weights
            $criteriaWeightsRaw = DB::table('criteria_weights')
                ->where('is_active', true)
                ->pluck('weight', 'criteria');

            $totalWeight = $criteriaWeightsRaw->sum();
            $criteriaWeights = [];
            foreach ($criteriaWeightsRaw as $criteria => $weight) {
                $criteriaWeights[$criteria] = $weight / $totalWeight;
            }

            // Calculate raw scores
            $financeData = [];
            $operationalData = [];
            $bdData = [];
            $storeData = [];

            foreach ($allStores as $store) {
                // Check data completeness
                $financeCount = $store->finances()
                    ->where('is_active', true)
                    ->where('period', '>=', now()->subMonths($monthBack))
                    ->count();

                $operationalCount = $store->operationals()
                    ->where('is_active', true)
                    ->where('period', '>=', now()->subMonths($monthBack))
                    ->count();

                $bdCount = $store->bds()
                    ->where('is_active', true)
                    ->where('period', '>=', now()->subMonths($monthBack))
                    ->count();

                $storeCount = $store->stores()
                    ->where('is_active', true)
                    ->where('period', '>=', now()->subMonths($monthBack))
                    ->count();

                // Calculate completeness
                $totalPossible = $monthBack * 4; // 4 categories per month
                $actualCount = $financeCount + $operationalCount + $bdCount + $storeCount;
                $completeness = ($actualCount / $totalPossible) * 100;

                // Initialize scores
                $finance = 0;
                $operational = 0;
                $bdScore = 0;
                $storeScore = 0;
                $dataComplete = $completeness >= 100;

                if ($dataComplete) {
                    // Calculate finance score
                    $finance = $store->finances()
                        ->where('is_active', true)
                        ->where('period', '>=', now()->subMonths($monthBack))
                        ->sum('net_profit_margin');

                    // Calculate operational score
                    $operational = $store->operationals()
                        ->where('is_active', true)
                        ->where('period', '>=', now()->subMonths($monthBack))
                        ->sum('total');

                    // Calculate BD score
                    $bdItems = $store->bds()
                        ->where('is_active', true)
                        ->where('period', '>=', now()->subMonths($monthBack))
                        ->get();

                    foreach ($bdItems as $item) {
                        $bdScore += ($item->direct_competition ?? 0) * 1;
                        $bdScore += ($item->indirect_competition ?? 0) * 1;
                        $bdScore += ($item->substitute_competition ?? 0) * 1;
                    }

                    // Calculate store score
                    $storeInputs = $store->stores()
                        ->where('is_active', true)
                        ->where('period', '>=', now()->subMonths($monthBack))
                        ->get();

                    foreach ($storeInputs as $storeInput) {
                        $storeScore += (int) $storeInput->aksesibilitas;
                        $storeScore += (int) $storeInput->visibilitas;
                        $storeScore += (int) $storeInput->lingkungan;
                        $storeScore += (int) $storeInput->lalu_lintas;
                        $storeScore += (int) $storeInput->area_parkir;
                    }
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
                $store->data_complete = $dataComplete;
                $store->completeness = $completeness;
            }

            // Calculate min & max for normalization (only complete data)
            $completeFinanceData = array_filter($financeData, fn($val) => $val > 0);
            $completeOperationalData = array_filter($operationalData, fn($val) => $val > 0);
            $completeBdData = array_filter($bdData, fn($val) => $val > 0);
            $completeStoreData = array_filter($storeData, fn($val) => $val > 0);

            $minMax = [
                'finance'     => ['min' => !empty($completeFinanceData) ? min($completeFinanceData) : 0, 'max' => !empty($completeFinanceData) ? max($completeFinanceData) : 0],
                'operational' => ['min' => !empty($completeOperationalData) ? min($completeOperationalData) : 0, 'max' => !empty($completeOperationalData) ? max($completeOperationalData) : 0],
                'bd'          => ['min' => !empty($completeBdData) ? min($completeBdData) : 0, 'max' => !empty($completeBdData) ? max($completeBdData) : 0],
                'store'       => ['min' => !empty($completeStoreData) ? min($completeStoreData) : 0, 'max' => !empty($completeStoreData) ? max($completeStoreData) : 0],
            ];

            // Calculate final scores
            $scoredStores = $allStores->map(function ($store) use ($criteriaWeights, $minMax) {
                if (!$store->data_complete) {
                    $store->final_score = 0;
                    $store->status = 'Data Belum Lengkap';
                    return $store;
                }

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
                return $store;
            });

            // Calculate mean score from complete data
            $completeStores = $scoredStores->filter(fn($store) => $store->data_complete);
            $meanScore = $completeStores->avg('final_score');

            // Determine status based on mean score comparison
            $scoredStores = $scoredStores->map(function ($store) use ($meanScore) {
                if (!$store->data_complete) {
                    return $store;
                }

                $store->status = $store->final_score >= $meanScore ? 'Layak Buka' : 'Layak Tutup';
                $store->above_mean = $store->final_score >= $meanScore;
                return $store;
            });

            // Sort stores: complete first (by score), then incomplete
            $sortedStores = $scoredStores->sortByDesc(function ($store) {
                return $store->data_complete ? $store->final_score : 0;
            })->values();

            // Manual pagination
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

            return view('review-store', [
                'paginatedStores' => $paginatedStores,
                'periodChoice' => $periodChoice,
                'closed' => $closed,
                'meanScore' => round($meanScore, 2)
            ]);

        } catch (\Throwable $e) {
            Log::error('StoreReview@index error: ' . $e->getMessage());
            return back()->with('error', 'Gagal memuat data.');
        }
    }



    public function update(Request $request, Store $store)
    {
        if (auth()->user()->role->role_name !== 'Manager Business Development') {
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

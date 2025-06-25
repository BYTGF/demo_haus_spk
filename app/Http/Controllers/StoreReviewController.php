<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\CriteriaWeight;
use App\Models\InputStore;
use App\Models\InputFinance;
use App\Models\InputOperational;
use App\Models\InputBD;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StoreReviewController extends Controller
{
    public function index(Request $request)
    {
        // Authorization check
        if (!in_array(auth()->user()->role->role_name, ['Manager Business Development', 'C-Level'])) {
            abort(403, 'Akses ditolak.');
        }

        $periodChoice = $request->get('period', '6');
        $monthBack = (int) $periodChoice;

        try {
            // Get all active stores (excluding store with id 1)
            $allStores = Store::where('is_active', true)
                            ->where('id', '!=', 1)
                            ->get();

            // Get criteria weights
            $criteriaWeights = DB::table('criteria_weights')
                            ->where('is_active', true)
                            ->pluck('weight', 'criteria')
                            ->map(fn($weight) => $weight / 100)
                            ->toArray();

            // Initialize arrays for min/max calculations
            $financeData = $operationalData = $bdData = $storeData = [];

            foreach ($allStores as $store) {
                // Check data completeness for all categories
                $counts = [
                    'finance' => $store->finances()->complete()->lastMonths($monthBack)->count(),
                    'operational' => $store->operationals()->complete()->lastMonths($monthBack)->count(),
                    'bd' => $store->bds()->complete()->lastMonths($monthBack)->count(),
                    'store' => $store->stores()->complete()->lastMonths($monthBack)->count()
                ];

                $totalPossible = $monthBack * 4;
                $actualCount = array_sum($counts);
                $completeness = ($actualCount / $totalPossible) * 100;
                $dataComplete = $completeness >= 100;

                // Initialize scores
                $scores = [
                    'finance' => 0,
                    'operational' => 0,
                    'bd' => 0,
                    'store' => 0
                ];

                if ($dataComplete) {
                    // Calculate scores
                    $scores['finance'] = $store->finances()->complete()->lastMonths($monthBack)->sum('net_profit_margin');
                    $scores['operational'] = $store->operationals()->complete()->lastMonths($monthBack)->sum('total');

                    // BD Score (latest month only)
                    $latestBd = $store->bds()->complete()->lastMonths($monthBack)->latest('period')->first();
                    if ($latestBd) {
                        $scores['bd'] = ($latestBd->direct_competition ?? 0) 
                                    + ($latestBd->indirect_competition ?? 0) 
                                    + ($latestBd->substitute_competition ?? 0);
                    }

                    // Store Score (latest month only)
                    $latestStore = $store->stores()->complete()->lastMonths($monthBack)->latest('period')->first();
                    if ($latestStore) {
                        $scores['store'] = $this->calculateStoreScore($latestStore);
                    }
                }

                // Store the scores and completeness
                $store->raw_scores = $scores;
                $store->data_complete = $dataComplete;
                $store->completeness = $completeness;

                // Push to arrays for min/max calculation
                foreach ($scores as $key => $value) {
                    ${$key.'Data'}[] = $value;
                }
            }

            // Calculate min/max for normalization
            $minMax = [
                'finance' => $this->calculateMinMax($financeData),
                'operational' => $this->calculateMinMax($operationalData),
                'bd' => $this->calculateMinMax($bdData),
                'store' => $this->calculateMinMax($storeData)
            ];

            // Calculate final scores with normalization
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
                    $isBenefit = in_array($key, ['finance', 'store']);

                    $normalized[$key] = $max !== $min 
                        ? ($isBenefit ? ($value / $max) : (($max - $value) / ($max - $min)))
                        : 0;
                }

                $finalScore = 0;
                foreach ($normalized as $key => $value) {
                    $finalScore += $value * ($criteriaWeights[$key] ?? 0);
                }

                $store->final_score = round($finalScore, 2);
                return $store;
            });

            // Calculate mean score and determine status
            $completeStores = $scoredStores->filter(fn($store) => $store->data_complete);
            $meanScore = $completeStores->isNotEmpty() ? $completeStores->avg('final_score') : 0;

            $scoredStores = $scoredStores->map(function ($store) use ($meanScore) {
                if ($store->data_complete) {
                    $store->status = $store->final_score >= $meanScore ? 'Layak Buka' : 'Layak Tutup';
                    $store->above_mean = $store->final_score >= $meanScore;
                }
                return $store;
            });

            // Sort and paginate
            $sortedStores = $scoredStores->sortBy(fn($store) => $store->data_complete ? $store->final_score : 0);
            $paginatedStores = new \Illuminate\Pagination\LengthAwarePaginator(
                $sortedStores->forPage($request->get('page', 1), 5),
                $sortedStores->count(),
                5,
                null,
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

    // Helper method to calculate store score
    protected function calculateStoreScore($store)
    {
        $lingkunganArray = json_decode($store->lingkungan, true);
        $lingkunganCount = is_array($lingkunganArray) ? count($lingkunganArray) : 0;

        $score = 0;
        
        // Accessibility
        $score += (int) ($store->aksesibilitas - 1);
        
        // Visibility
        $score += $store->visibilitas > 100 ? 1 : 0;
        
        // Environment
        $score += match($lingkunganCount) {
            3 => 2,
            2 => 1,
            default => 0
        };
        
        // Traffic
        $score += $store->lalu_lintas >= 4 ? 1 : 0;
        
        // Vehicle density
        $score += match($store->kepadatan_kendaraan) {
            3 => 2,
            2 => 1,
            default => 0
        };
        
        // Parking
        $score += ($store->parkir_mobil >= 1 && $store->parkir_motor >= 3) ? 1 : 0;
        
        return $score;
    }

    // Helper method to calculate min/max
    protected function calculateMinMax(array $data)
    {
        $filtered = array_filter($data, fn($val) => $val > 0);
        return [
            'min' => !empty($filtered) ? min($filtered) : 0,
            'max' => !empty($filtered) ? max($filtered) : 0
        ];
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

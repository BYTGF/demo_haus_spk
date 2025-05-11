<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Area;
use App\Models\Store;
use App\Models\InputFinance;
use App\Models\InputStore;
use App\Models\InputOperational;
use App\Models\InputBD;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = auth()->user();

            $roleName = optional($user->role)->role_name;

            $storeFilter = $request->input('store_filter', 'all'); // 'all' or specific store ID
            $periodFilter = $request->input('period_filter', 'all');
            
            $availablePeriods = InputFinance::selectRaw('DISTINCT DATE_FORMAT(period, "%Y-%m") as period')
            ->orderByDesc('period')
            ->pluck('period');

            // Get list of stores for dropdown (only for Manager)
            $stores = [];
            if ($user->role->role_name === 'Manager Business Development') {
                $stores = Store::where('id', '!=', 1)->pluck('store_name', 'id');

            } elseif ($user->role->role_name === 'Area Manager') {
                $stores = Store::where('area_id', $user->area_id)
                            ->where('id', '!=', 1)
                            ->pluck('store_name', 'id');
            }


            //Operational
            // For Business Development Manager - can see all or filter by store
            $inputOperationals = collect(); 
            if ($user->role->role_name === 'Manager Business Development') {
                $inputOperationals = InputOperational::with(['user', 'store'])
                    ->when($storeFilter !== 'all', function ($query) use ($storeFilter) {
                        $query->where('store_id', $storeFilter);
                    })
                    ->when($periodFilter !== 'all', fn($q) => $q->whereMonth('period', substr($periodFilter, 5, 2))
                    ->whereYear('period', substr($periodFilter, 0, 4)))
                    ->get();
            } 
            // For Operational Staff - only their store
            elseif ($user->role->role_name === 'Operational') {
                $inputOperationals = InputOperational::with(['user', 'store'])
                    ->where('store_id', $user->store_id)
                    ->when($periodFilter !== 'all', fn($q) => $q->whereMonth('period', substr($periodFilter, 5, 2))
                    ->whereYear('period', substr($periodFilter, 0, 4)))
                    ->get();
                $storeFilter = $user->store_id; // Force to their store
            }

            $operationalData = [
                'Personnel & Facilities' => $inputOperationals->sum(fn($item) => ($item->gaji_upah ?? 0) + ($item->sewa ?? 0) + ($item->utilitas ?? 0)),
                'Supplies' => $inputOperationals->sum('perlengkapan') ?? 0,
                'Others' => $inputOperationals->sum('lain_lain') ?? 0,
                'Total' => $inputOperationals->sum('total') ?? 0,
                'Status' => optional($inputOperationals->first())->status ?? '',
            ];

            //Finance
            // For Business Development Manager - can see all or filter by store
            $inputFinances = collect(); 
            if ($user->role->role_name === 'Manager Business Development') {
                $inputFinances = InputFinance::with(['user', 'store'])
                    ->when($storeFilter !== 'all', function ($query) use ($storeFilter) {
                        $query->where('store_id', $storeFilter);
                    })
                    ->when($periodFilter !== 'all', fn($q) => $q->whereMonth('period', substr($periodFilter, 5, 2))
                    ->whereYear('period', substr($periodFilter, 0, 4)))
                    ->get();
            } 
            // For Operational Staff - only their store
            elseif ($user->role->role_name === 'Finance') {
                $inputFinances = InputFinance::with(['user', 'store'])
                    ->where('store_id', $user->store_id)
                    ->when($periodFilter !== 'all', fn($q) => $q->whereMonth('period', substr($periodFilter, 5, 2))
                    ->whereYear('period', substr($periodFilter, 0, 4)))
                    ->get();
                $storeFilter = $user->store_id; // Force to their store
            }

            //Store
            // For Business Development Manager - can see all or filter by store
            $inputStores = collect(); 
            if ($user->role->role_name === 'Manager Business Development') {
                $inputStores = InputOperational::with(['user', 'store'])
                    ->when($storeFilter !== 'all', function ($query) use ($storeFilter) {
                        $query->where('store_id', $storeFilter);
                    })
                    ->when($periodFilter !== 'all', fn($q) => $q->whereMonth('period', substr($periodFilter, 5, 2))
                    ->whereYear('period', substr($periodFilter, 0, 4)))
                    ->get();
            } 
            // For Operational Staff - only their store
            elseif ($user->role->role_name === 'Store Manager') {
                $inputStores = InputOperational::with(['user', 'store'])
                    ->where('store_id', $user->store_id)
                    ->when($periodFilter !== 'all', fn($q) => $q->whereMonth('period', substr($periodFilter, 5, 2))
                    ->whereYear('period', substr($periodFilter, 0, 4)))
                    ->get();
                $storeFilter = $user->store_id; // Force to their store
            }

            //BD
            // For Business Development Manager - can see all or filter by store
            $inputbds = collect(); 
            if ($user->role->role_name === 'Manager Business Development' || $user->role->role_name === 'Business Development Staff') {
                $inputbds = InputBD::with(['user', 'store'])
                    ->when($storeFilter !== 'all', function ($query) use ($storeFilter) {
                        $query->where('store_id', $storeFilter);
                    })
                    ->when($periodFilter !== 'all', fn($q) => $q->whereMonth('period', substr($periodFilter, 5, 2))
                    ->whereYear('period', substr($periodFilter, 0, 4)))
                    ->get();
            }

            // Update the completion data calculation in index()
            $completionData = [];
            foreach ($stores as $storeId => $storeName) {
                $completed = 0;
                $totalPossible = 4; // Finance, Operational, Store, BD

                // Check each data type with period filter
                $completed += InputFinance::when($periodFilter !== 'all', fn($q) => $q->whereMonth('period', substr($periodFilter, 5, 2))
                            ->whereYear('period', substr($periodFilter, 0, 4)))
                            ->where('store_id', $storeId)
                            ->exists() ? 1 : 0;
                
                // Repeat for other input types...
                $completed += InputOperational::when($periodFilter !== 'all', fn($q) => $q->whereMonth('period', substr($periodFilter, 5, 2))
                            ->whereYear('period', substr($periodFilter, 0, 4)))
                            ->where('store_id', $storeId)
                            ->exists() ? 1 : 0;
                
                $completed += InputBD::when($periodFilter !== 'all', fn($q) => $q->whereMonth('period', substr($periodFilter, 5, 2))
                            ->whereYear('period', substr($periodFilter, 0, 4)))
                            ->where('store_id', $storeId)
                            ->exists() ? 1 : 0;

                $completed += InputStore::when($periodFilter !== 'all', fn($q) => $q->whereMonth('period', substr($periodFilter, 5, 2))
                ->whereYear('period', substr($periodFilter, 0, 4)))
                ->where('store_id', $storeId)
                ->exists() ? 1 : 0;

                // ... same for InputStore and InputBD

                $completionData[$storeId] = [
                    'name' => $storeName,
                    'completed' => $completed,
                    'total' => $totalPossible,
                    'percentage' => $totalPossible > 0 ? ($completed / $totalPossible) * 100 : 0
                ];
            }
            
            return view('dashboard', compact('inputFinances', 'inputOperationals', 'inputStores', 'inputbds','operationalData', 'stores', 'storeFilter', 'periodFilter', 'availablePeriods', 'completionData'));
            
        } catch (\Exception $e) {
            \Log::error('Dashboard Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menampilkan data dashboard.');
        }
    }

    public function getChartData(Request $request)
    {
        $user = auth()->user();
        $roleName = optional($user->role)->role_name;
        $storeFilter = $request->input('store_filter', 'all');

        $query = InputOperational::query();

        $inputOperationals = collect(); 

        if ($user->role->role_name === 'Manager Business Development') {
            if ($storeFilter !== 'all') {
                $query->where('store_id', $storeFilter);
            }
        } else {
            $query->where('store_id', $user->store_id);
        }

        $data = [
            'Personnel & Facilities' => $inputOperationals->sum(fn($item) => ($item->gaji_upah ?? 0) + ($item->sewa ?? 0) + ($item->utilitas ?? 0)),
            'Supplies' => $inputOperationals->sum('perlengkapan') ?? 0,
            'Others' => $inputOperationals->sum('lain_lain') ?? 0,
            'Total' => $inputOperationals->sum('total') ?? 0,
            'Status' => optional($inputOperationals->first())->status ?? '',
        ];

        return response()->json($data);
    }

    public function getCompletionData(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get("start");
        $length = $request->get("length");
        $search = $request->get('search')['value'] ?? '';
        $periodFilter = $request->get('period_filter', 'all');

        // Base query
        $query = Store::where('id', '!=', 1)
        ->withCount([
            'finances' => function($q) use ($periodFilter) {
                if ($periodFilter !== 'all') {
                    $q->whereMonth('period', substr($periodFilter, 5, 2))
                      ->whereYear('period', substr($periodFilter, 0, 4));
                }
            },
            'operationals' => function($q) use ($periodFilter) {
                if ($periodFilter !== 'all') {
                    $q->whereMonth('period', substr($periodFilter, 5, 2))
                      ->whereYear('period', substr($periodFilter, 0, 4));
                }
            },
            'stores' => function($q) use ($periodFilter) {
                if ($periodFilter !== 'all') {
                    $q->whereMonth('period', substr($periodFilter, 5, 2))
                      ->whereYear('period', substr($periodFilter, 0, 4));
                }
            },
            'bds' => function($q) use ($periodFilter) {
                if ($periodFilter !== 'all') {
                    $q->whereMonth('period', substr($periodFilter, 5, 2))
                      ->whereYear('period', substr($periodFilter, 0, 4));
                }
            },
            // Repeat for other relations...
        ]);

        // Apply search filter
        if (!empty($search)) {
            $query->where('store_name', 'like', '%'.$search.'%');
        }

        // Get total records count
        $totalRecords = $query->count();

        // Paginate results
        $stores = $query->offset($start)
                    ->limit($length)
                    ->get();

        // Format data for DataTables
        $data = [];
        foreach ($stores as $store) {
            $completed = 0;
            $completed += $store->finance_inputs_count > 0 ? 1 : 0;
            $completed += $store->operational_inputs_count > 0 ? 1 : 0;
            $completed += $store->store_inputs_count > 0 ? 1 : 0;
            $completed += $store->bd_inputs_count > 0 ? 1 : 0;
            
            $percentage = ($completed / 4) * 100;

            $data[] = [
                'DT_RowId' => 'row_'.$store->id, // Optional: for row styling
                'name' => $store->store_name,
                'progress' => view('partials.progress-bar', [
                    'percentage' => $percentage,
                    'completed' => $completed
                ])->render(),
                'status' => $completed.'/4'
            ];
        }

        return response()->json([
            "draw" => $draw,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalRecords,
            "data" => $data
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Area;
use App\Models\Store;
use App\Models\InputFinance;
use App\Models\InputStore;
use App\Models\InputOperational;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        \Log::info('Current user: ', ['user' => auth()->user()]);
        try {
            $user = auth()->user();

            $roleName = optional($user->role)->role_name;

            $inputFinances = InputFinance::with('user', 'store')
            ->when($user->role->role_name === 'Finance', function ($query) use ($user) {
                // Finance cuma lihat data dari store di area-nya
                $query->whereHas('store', function ($storeQuery) use ($user) {
                    $storeQuery->where('area_id', $user->area_id);
                });
            })
            ->get();

            $inputStores = InputStore::with('user', 'store')
            ->when($user->role->role_name === 'Area Manager', function ($query) use ($user) {
                // Manager lihat semua store dalam area-nya
                $query->whereHas('store', function ($storeQuery) use ($user) {
                    $storeQuery->where('area_id', $user->area_id);
                });
                })
            ->when($user->role->role_name === 'Store Manager', function ($query) use ($user) {
                // Staff Finance cuma lihat store miliknya sendiri
                $query->where('store_id', $user->store_id);
            })
            ->get();

            $storeFilter = $request->input('store_filter', 'all'); // 'all' or specific store ID

            $inputOperationals = collect(); 

            // For Business Development Manager - can see all or filter by store
            if ($user->role->role_name === 'Manager Business Development') {
                $inputOperationals = InputOperational::with(['user', 'store'])
                    ->when($storeFilter !== 'all', function ($query) use ($storeFilter) {
                        $query->where('store_id', $storeFilter);
                    })
                    ->get();
            } 
            // For Operational Staff - only their store
            elseif ($user->role->role_name === 'Operational') {
                $inputOperationals = InputOperational::with(['user', 'store'])
                    ->where('store_id', $user->store_id)
                    ->get();
                $storeFilter = $user->store_id; // Force to their store
            }

            // Get list of stores for dropdown (only for Manager)
            $stores = [];
            if ($user->role->role_name === 'Manager Business Development') {
                $stores = Store::where('id', '!=', 1)->pluck('store_name', 'id');
            }

            // Prepare chart data
            $operationalData = [
                'Personnel & Facilities' => $inputOperationals->sum(fn($item) => ($item->gaji_upah ?? 0) + ($item->sewa ?? 0) + ($item->utilitas ?? 0)),
                'Supplies' => $inputOperationals->sum('perlengkapan') ?? 0,
                'Others' => $inputOperationals->sum('lain_lain') ?? 0,
                'Total' => $inputOperationals->sum('Total') ?? 0,
                'Status' => optional($inputOperationals->first())->status ?? '',
            ];
    
            // dd($operationalData);
            return view('dashboard', compact('inputFinances', 'inputOperationals', 'inputStores', 'operationalData', 'stores', 'storeFilter'));
            
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
            'Total' => $inputOperationals->sum('Total') ?? 0,
            'Status' => optional($inputOperationals->first())->status ?? '',
        ];

        return response()->json($data);
    }
}

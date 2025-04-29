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
    public function index()
    {
        try {
            $user = auth()->user();
            $inputFinance = InputFinance::with(['user', 'store'])->latest()->get();
            $inputOperational = InputOperational::with(['user', 'store'])->latest()->get();

            $inputStore = InputStore::with('user', 'store')
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
    
    
            return view('dashboard', compact('inputFinance', 'inputOperational', 'inputStore'));
            
        } catch (\Exception $e) {
            \Log::error('Dashboard Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menampilkan data dashboard.');
        }
    }
}

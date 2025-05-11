<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Store;
use App\Models\InputFinance;
use App\Models\InputOperational;

class InputFinanceController extends Controller
{
    public function index()
    {
        try {
            $user = auth()->user();
            $dones = InputFinance::with('user', 'store')
            ->when($user->role->role_name === 'Finance', function ($query) use ($user) {
                // Staff Finance cuma lihat store miliknya sendiri
                $query->where('store_id', $user->store_id);
            })
            ->where('status', 'Selesai')
            ->latest()
            ->paginate(10, ['*'], 'dones_page');
            
            $inputs = InputFinance::with('user', 'store')
            ->when($user->role->role_name === 'Finance', function ($query) use ($user) {
                // Staff Finance cuma lihat store miliknya sendiri
                $query->where('store_id', $user->store_id);
            })
            ->whereIn('status', ['Sedang Direview', 'Butuh Revisi'])
            ->latest()
            ->paginate(10, ['*'], 'inputs_page');
                
            // $stores = Store::where('id', '!=', 1)->get();

            return view('finance', compact('dones', 'inputs'));
        } catch (\Exception $e) {
            \Log::error('Error fetching data in index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load operational data.');
        }
    }

    public function store(Request $request)
    {
        if (auth()->user()->role->role_name === 'Finance') {
            $validated = $request->validate([
                // 'period' => 'required|date',
                'penjualan' => 'required|numeric|min:0',
                'pendapatan_lain' => 'required|numeric|min:0',
                'total_hpp' => 'required|numeric|min:0',
                // 'store_id' => 'required|exists:stores,id',
                'comment_input' => 'nullable|string',
            ]);

            $exists = InputFinance::where('store_id', $request->store_id)
                ->where('period', $request->period)
                ->exists();

            if ($exists) {
                return redirect()->back()
                    ->withErrors(['period' => 'Kamu sudah input data untuk periode ini.'])
                    ->withInput();
            }

            // Calculate derived fields
            $validated['total_pendapatan'] = $validated['penjualan'] + $validated['pendapatan_lain'];
            $validated['laba_kotor'] = $validated['total_pendapatan'] - $validated['total_hpp'];
            
            // Get operational costs from the selected store
            $operational = InputOperational::where('store_id', auth()->user()->store_id)
            ->latest()
            ->first();

            if (!$operational) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['operational' => 'Data biaya operasional belum tersedia. Harap input terlebih dahulu.']);
            }
                    
            $validated['biaya_operasional'] = $operational->total ?? 0;
            $validated['laba_sebelum_pajak'] = $validated['laba_kotor'] - $validated['biaya_operasional'];
            $validated['laba_bersih'] = $validated['laba_sebelum_pajak'];
            
            // Calculate margins
            $validated['gross_profit_margin'] = $validated['penjualan'] > 0 
                ? ($validated['laba_kotor'] / $validated['penjualan']) * 100 
                : 0;
                
            $validated['net_profit_margin'] = $validated['penjualan'] > 0 
                ? ($validated['laba_bersih'] / $validated['penjualan']) * 100 
                : 0;
            
            
            $validated['user_id'] = auth()->id();
            $validated['period'] = now()->format('Y-m-d');
            $validated['store_id'] = auth()->user()->store_id;
            $validated['status'] = 'Sedang Direview';

            InputFinance::create($validated);

            return redirect()->route('finance.index')
                ->with('success', 'Finance input submitted for approval');
        }

        abort(403, 'Unauthorized action.');
    }

    public function approve(InputFinance $finance)
    {
        if (auth()->user()->role->role_name === 'Manager Business Development') {
            $finance->update([
                'status' => 'Selesai',
                'comment_review' => request('comment_review', 'Approved by Manager')
            ]);

            return redirect()->route('finance.index')
                ->with('success', 'Finance input approved successfully');
        }

        abort(403, 'Unauthorized action.');
    }

    public function reject($id)
    {
        $finance = InputFinance::findOrFail($id);
        
        request()->validate([
            'comment_review' => 'required|string'
        ]);

        $finance->update([
            'status' => 'Butuh Revisi',
            'comment_review' => request('comment_review')
        ]);

        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id)
    {
        if (auth()->user()->role->role_name === 'Finance') {
            $finance = InputFinance::findOrFail($id);
            
            $validated = $request->validate([
                'period' => 'required|date',
                'penjualan' => 'required|numeric|min:0',
                'pendapatan_lain' => 'required|numeric|min:0',
                'total_hpp' => 'required|numeric|min:0',
                'store_id' => 'required|exists:stores,id',
                'comment_input' => 'nullable|string',
                'comment_review' => 'nullable|string',
            ]);

            // Recalculate all derived fields
            $validated['total_pendapatan'] = $validated['penjualan'] + $validated['pendapatan_lain'];
            $validated['laba_kotor'] = $validated['total_pendapatan'] - $validated['total_hpp'];
            
            $operational = InputOperational::where('store_id', $validated['store_id'])
                ->latest()
                ->first();
                
            $validated['biaya_operasional'] = $operational->total ?? 0;
            $validated['laba_sebelum_pajak'] = $validated['laba_kotor'] - $validated['biaya_operasional'];
            $validated['laba_bersih'] = $validated['laba_sebelum_pajak'];
            
            $validated['gross_profit_margin'] = $validated['penjualan'] > 0 
                ? ($validated['laba_kotor'] / $validated['penjualan']) * 100 
                : 0;
                
            $validated['net_profit_margin'] = $validated['penjualan'] > 0 
                ? ($validated['laba_bersih'] / $validated['penjualan']) * 100 
                : 0;
            
            $validated['status'] = 'Sedang Direview'; // Reset status when updated

            $finance->update($validated);

            return redirect()->route('finance.index')
                ->with('success', 'Finance input updated successfully');
        }

        abort(403, 'Unauthorized action.');
    }
}

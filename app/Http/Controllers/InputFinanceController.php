<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Store;
use App\Models\InputFinance;
use App\Models\InputOperational;
use Carbon\Carbon;

class InputFinanceController extends Controller
{
    public function index()
    {
        try {
            $user = auth()->user();
            $dones = InputFinance::with('user', 'store')
            ->when($user->role->role_name === 'Finance', function ($query) use ($user) {
                // Staff Finance cuma lihat store miliknya sendiri
                $query->where('store_id', $user->store_id)->where('is_active', true);
            })
            ->where('status', 'Selesai')
            ->latest()
            ->paginate(10, ['*'], 'dones_page');
            
            $inputs = InputFinance::with('user', 'store')
            ->when($user->role->role_name === 'Finance', function ($query) use ($user) {
                // Staff Finance cuma lihat store miliknya sendiri
                $query->where('store_id', $user->store_id)->where('is_active', true);
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
        try {
            if (auth()->user()->role->role_name === 'Finance') {
                $validated = $request->validate([
                    'period' => 'required|date_format:Y-m',
                    'penjualan' => 'required|numeric|min:0',
                    'pendapatan_lain' => 'required|numeric|min:0',
                    'total_hpp' => 'required|numeric|min:0',
                    'comment_input' => 'nullable|string',
                ]);

                $period = Carbon::parse($request->period);

                $exists = InputFinance::where('store_id', $request->store_id)
                    ->whereMonth('period', $period->month)
                    ->whereYear('period', $period->year)
                    ->exists();


                if ($exists) {
                    return redirect()->back()
                        ->withErrors(['period' => 'Kamu sudah input data untuk periode ini.'])
                        ->withInput();
                }

                $validated['total_pendapatan'] = $validated['penjualan'] + $validated['pendapatan_lain'];
                $validated['laba_kotor'] = $validated['total_pendapatan'] - $validated['total_hpp'];

                $operational = InputOperational::where('store_id', auth()->user()->store_id)->latest()->first();

                if (!$operational) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['operational' => 'Data biaya operasional belum tersedia. Harap input terlebih dahulu.']);
                }

                $validated['biaya_operasional'] = $operational->total ?? 0;
                $validated['laba_sebelum_pajak'] = $validated['laba_kotor'] - $validated['biaya_operasional'];
                $validated['laba_bersih'] = $validated['laba_sebelum_pajak'];

                $validated['gross_profit_margin'] = $validated['penjualan'] > 0
                    ? ($validated['laba_kotor'] / $validated['penjualan']) * 100
                    : 0;

                $validated['net_profit_margin'] = $validated['penjualan'] > 0
                    ? ($validated['laba_bersih'] / $validated['penjualan']) * 100
                    : 0;

                $validated['user_id'] = auth()->id();
                $validated['period'] = $validated['period'] . '-15'; // Format ke YYYY-MM-DD
                $validated['store_id'] = auth()->user()->store_id;
                $validated['status'] = 'Sedang Direview';

                InputFinance::create($validated);

                return redirect()->route('finance.index')
                    ->with('success', 'Input finance berhasil dikirim untuk direview.');
            }

            abort(403, 'Unauthorized action.');
        } catch (\Throwable $e) {
            \Log::error('Error fetching data in index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function getOperationalData(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'period' => 'required|date_format:Y-m', // Format dari input month (YYYY-MM)
                'store_id' => 'required|exists:stores,id'
            ]);

            // Konversi period dari YYYY-MM ke YYYY-MM-DD (format database)
            $periodInDB = Carbon::parse($request->period); // Menjadi 2025-06-01

            $operational = InputOperational::where('store_id', $request->store_id)
                ->whereMonth('period', $periodInDB)
                ->whereYear('period', $periodInDB)
                ->first();

            if (!$operational) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data operasional tidak ditemukan untuk periode ' . $request->period
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'biaya_operasional' => $operational->total
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function approve(InputFinance $finance)
    {
        try {
            if (auth()->user()->role->role_name === 'Manager Business Development') {
                $finance->update([
                    'status' => 'Selesai',
                    'comment_review' => request('comment_review', 'Approved by Manager')
                ]);

                return redirect()->route('finance.index')
                    ->with('success', 'Input finance berhasil di-approve.');
            }

            abort(403, 'Unauthorized action.');
        } catch (\Throwable $e) {
            \Log::error('Error fetching data in index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal approve data: ' . $e->getMessage());
        }
    }

    public function reject($id)
    {
        try {
            $finance = InputFinance::findOrFail($id);

            request()->validate([
                'comment_review' => 'required|string'
            ]);

            $finance->update([
                'status' => 'Butuh Revisi',
                'comment_review' => request('comment_review')
            ]);

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            \Log::error('Error fetching data in index: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            if (auth()->user()->role->role_name === 'Finance') {
                $finance = InputFinance::findOrFail($id);

                $validated = $request->validate([
                    'period' => 'required|date',
                    'penjualan' => 'required|numeric|min:0',
                    'pendapatan_lain' => 'required|numeric|min:0',
                    'total_hpp' => 'required|numeric|min:0',
                    'comment_input' => 'nullable|string',
                    'comment_review' => 'nullable|string',
                ]);

                $validated['total_pendapatan'] = $validated['penjualan'] + $validated['pendapatan_lain'];
                $validated['laba_kotor'] = $validated['total_pendapatan'] - $validated['total_hpp'];

                $operational = InputOperational::where('store_id', $finance->store_id)->latest()->first();
                $validated['biaya_operasional'] = $operational->total ?? 0;
                $validated['laba_sebelum_pajak'] = $validated['laba_kotor'] - $validated['biaya_operasional'];
                $validated['laba_bersih'] = $validated['laba_sebelum_pajak'];

                $validated['gross_profit_margin'] = $validated['penjualan'] > 0
                    ? ($validated['laba_kotor'] / $validated['penjualan']) * 100
                    : 0;

                $validated['net_profit_margin'] = $validated['penjualan'] > 0
                    ? ($validated['laba_bersih'] / $validated['penjualan']) * 100
                    : 0;

                $validated['status'] = 'Sedang Direview';

                $finance->update($validated);

                return redirect()->route('finance.index')
                    ->with('success', 'Input finance berhasil diupdate.');
            }

            abort(403, 'Unauthorized action.');
        } catch (\Throwable $e) {
            \Log::error('Error fetching data in index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal update data: ' . $e->getMessage());
        }
    }
}

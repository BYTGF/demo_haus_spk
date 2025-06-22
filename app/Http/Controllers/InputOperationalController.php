<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
// use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Store;
use App\Models\InputOperational;
use Carbon\Carbon;

class InputOperationalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $user = auth()->user();
            $dones = InputOperational::with('user', 'store')
            ->when($user->role->role_name === 'Operational', function ($query) use ($user) {
                // Staff Finance cuma lihat store miliknya sendiri
                $query->where('store_id', $user->store_id)->where('is_active', true);
            })
            ->where('status', 'Selesai')
            ->latest()
            ->paginate(10, ['*'], 'dones_page');
            
            $inputs = InputOperational::with('user', 'store')
            ->when($user->role->role_name === 'Operational', function ($query) use ($user) {
                // Staff Finance cuma lihat store miliknya sendiri
                $query->where('store_id', $user->store_id)->where('is_active', true);
            })
            ->whereIn('status', ['Sedang Direview', 'Butuh Revisi'])
            ->latest()
            ->paginate(10, ['*'], 'inputs_page');
                
            // $stores = Store::where('id', '!=', 1)->get();

            return view('operational', compact('dones', 'inputs'));
        } catch (\Exception $e) {
            \Log::error('Error fetching data in index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load operational data.');
        }
    }

    public function store(Request $request)
    {
        if (auth()->user()->role->role_name == 'Operational') {
            try {
                $validated = $request->validate([
                    'period' => 'required|date_format:Y-m',
                    'gaji_upah' => 'required|integer|min:0',
                    'sewa' => 'required|integer|min:0',
                    'utilitas' => 'required|integer|min:0',
                    'perlengkapan' => 'required|integer|min:0',
                    'lain_lain' => 'required|integer|min:0',
                    'comment_input' => 'nullable|string',
                ]);

                $period = Carbon::parse($request->period);

                $exists = InputOperational::where('store_id', $request->store_id)
                    ->whereMonth('period', $period->month)
                    ->whereYear('period', $period->year)
                    ->exists();


                if ($exists) {
                    return redirect()->back()->withErrors(['period' => 'Kamu sudah input data untuk periode ini.'])->withInput();
                }

                $validated['total'] = $validated['gaji_upah'] + $validated['sewa'] + $validated['utilitas'] + $validated['perlengkapan'] + $validated['lain_lain'];
                $validated['period'] = $validated['period'] . '-15'; // Format ke YYYY-MM-DD
                $validated['status'] = 'Sedang Direview';
                $validated['store_id'] = auth()->user()->store_id;
                $validated['user_id'] = auth()->id();

                InputOperational::create($validated);

                return redirect()->route('operational.index')->with('success', 'Operational input submitted for approval');
            } catch (\Exception $e) {
                \Log::error('Error fetching data in index: ' . $e->getMessage());
                return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan. Silakan coba lagi.']);
            }
        }

        abort(403, 'Unauthorized action.');
    }

    public function approve(InputOperational $input)
    {
        if (auth()->user()->role->role_name === 'Manager Business Development') {
            try {
                $input->update([
                    'status' => 'Selesai',
                    'comment_review' => request('comment_review', 'Approved')
                ]);

                return redirect()->route('operational.index')->with('success', 'Operational input approved successfully');
            } catch (\Exception $e) {
                \Log::error('Error fetching data in index: ' . $e->getMessage());
                return redirect()->back()->withErrors(['error' => 'Gagal menyetujui data.']);
            }
        }

        abort(403, 'Unauthorized action.');
    }

    public function reject($id)
    {
        try {
            $input = InputOperational::findOrFail($id);

            request()->validate([
                'comment_review' => 'required|string'
            ]);

            $input->update([
                'status' => 'Butuh Revisi',
                'comment_review' => request('comment_review')
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Error fetching data in index: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menolak data.']);
        }
    }

    public function update(Request $request, $id)
    {
        if (auth()->user()->role->role_name == 'Operational') {
            try {
                $operationalInput = InputOperational::findOrFail($id);

                $validated = $request->validate([
                    'period' => 'required|date',
                    'gaji_upah' => 'required|integer|min:0',
                    'sewa' => 'required|integer|min:0',
                    'utilitas' => 'required|integer|min:0',
                    'perlengkapan' => 'required|integer|min:0',
                    'lain_lain' => 'required|integer|min:0',
                    'comment_input' => 'nullable|string',
                    'comment_review' => 'nullable|string',
                ]);

                $validated['status'] = 'Sedang Direview';

                $validated['total'] = $validated['gaji_upah'] + $validated['sewa'] + $validated['utilitas'] + $validated['perlengkapan'] + $validated['lain_lain'];


                $operationalInput->update($validated);

                return redirect()->route('operational.index')->with('success', 'Operational input updated successfully.');
            } catch (\Exception $e) {
                \Log::error('Error fetching data in index: ' . $e->getMessage());
                return redirect()->back()->withErrors(['error' => 'Gagal update data operational.']);
            }
        }

        abort(403, 'Unauthorized action.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InputOperational $inputOperational)
    {
        //
    }
}

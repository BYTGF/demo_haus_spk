<?php

namespace App\Http\Controllers;

use App\Models\InputStore;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class InputStoreController extends Controller
{
    public function index()
    {
        try {
            $user = auth()->user();

            $dones = InputStore::with('user', 'store')
                ->when($user->role->role_name === 'Store Manager', function ($query) use ($user) {
                    $query->where('store_id', $user->store_id)->where('is_active', true);
                })
                ->when($user->role->role_name === 'Area Manager', function ($query) use ($user) {
                    $storeIds = Store::where('area_id', $user->area_id)->where('is_active', true)->pluck('id');
                    $query->whereIn('store_id', $storeIds);
                })
                ->where('status', 'Selesai')
                ->latest()
                ->paginate(10, ['*'], 'dones_page');

            $inputs = InputStore::with('user', 'store')
                ->when($user->role->role_name === 'Store Manager', function ($query) use ($user) {
                    $query->where('store_id', $user->store_id);
                })
                ->when($user->role->role_name === 'Area Manager', function ($query) use ($user) {
                    $storeIds = Store::where('area_id', $user->area_id)->pluck('id');
                    $query->whereIn('store_id', $storeIds);
                })
                ->whereIn('status', ['Sedang Direview Manager Area', 'Sedang Direview Manager BD', 'Butuh Revisi'])
                ->latest()
                ->paginate(10, ['*'], 'inputs_page');

            return view('store', compact('dones', 'inputs'));
        } catch (\Exception $e) {
            \Log::error('Error fetching data in index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load operational data.');
        }
    }


   public function store(Request $request)
    {
        if (auth()->user()->role->role_name === 'Store Manager') {
            \Log::info('Store evaluation input:', $request->all());
            try {
                $validated = $request->validate([
                    'period' => 'required|date_format:Y-m',
                    'aksesibilitas' => 'required|integer|between:1,4',
                    'visibilitas' => 'required|integer|min:0',
                    'lingkungan' => 'required|array|min:1',
                    'lingkungan.*' => 'integer|between:1,3',
                    'lalu_lintas' => 'required|integer|min:0',
                    'kepadatan_kendaraan' => 'required|integer|between:1,3',
                    'parkir_mobil' => 'required|integer|min:0',
                    'parkir_motor' => 'required|integer|min:0',
                    'comment_input' => 'nullable|string',
                ]);

                // Check for existing data
                $period = Carbon::parse($request->period);

                $exists = InputStore::where('store_id', $request->store_id)
                    ->whereMonth('period', $period->month)
                    ->whereYear('period', $period->year)
                    ->exists();


                if ($exists) {
                    return redirect()->back()
                        ->withErrors(['period' => 'Kamu sudah input data untuk periode ini.'])
                        ->withInput();
                }

                // Convert visibility input to score
                $visibilitasValue = (int)$validated['visibilitas'];
                if ($visibilitasValue < 20) {
                    $validated['visibilitas'] = 1;
                } elseif ($visibilitasValue >= 20 && $visibilitasValue < 40) {
                    $validated['visibilitas'] = 2;
                } elseif ($visibilitasValue >= 40 && $visibilitasValue < 60) {
                    $validated['visibilitas'] = 3;
                } elseif ($visibilitasValue >= 60 && $visibilitasValue < 80) {
                    $validated['visibilitas'] = 4;
                } else {
                    $validated['visibilitas'] = 5;
                }

                // Prepare data
                $validated['user_id'] = auth()->id();
                $validated['status'] = 'Sedang Direview Manager Area';
                $validated['period'] = $validated['period'] . '-15';
                $validated['store_id'] = auth()->user()->store_id;
                
                // Tambahkan ini untuk mengkonversi lingkungan ke JSON
                $validated['lingkungan'] = json_encode($validated['lingkungan']);

                InputStore::create($validated);

                return redirect()->route('store.index')
                    ->with('success', 'Store evaluation submitted for area manager review');
                    
            } catch (\Exception $e) {
                \Log::error('Store evaluation error: ' . $e->getMessage());
                return redirect()->back()
                    ->withErrors(['error' => 'Terjadi kesalahan. Silakan coba lagi.'])
                    ->withInput();
            }
        }
        abort(403, 'Unauthorized action.');
    }

    public function approveArea(InputStore $input)
    {
        if (auth()->user()->role->role_name === 'Area Manager') {
            try {
                $input->update([
                    'status' => 'Sedang Direview Manager BD',
                    'comment_review' => request('comment_review', 'Approved by Area Manager')
                ]);

                return redirect()->route('store.index')->with('success', 'Store evaluation approved and forwarded to Business Development Manager');
            } catch (\Exception $e) {
                return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat menyetujui data.']);
            }
        }

        abort(403, 'Unauthorized action.');
    }

    public function approveBd(InputStore $input)
    {
        if (auth()->user()->role->role_name === 'Manager Business Development') {
            try {
                $input->update([
                    'status' => 'Selesai',
                    'comment_review' => request('comment_review', 'Approved by Business Development Manager')
                ]);

                return redirect()->route('store.index')->with('success', 'Store evaluation approved successfully');
            } catch (\Exception $e) {
                \Log::error('Error fetching data in index: ' . $e->getMessage());
                return redirect()->back()->withErrors(['error' => 'Gagal menyetujui evaluasi.']);
            }
        }

        abort(403, 'Unauthorized action.');
    }

    public function reject($id)
    {
        try {
            $input = InputStore::findOrFail($id);

            request()->validate([
                'comment_review' => 'required|string'
            ]);

            $approvalLevel = request('approval_level');
            $commentPrefix = $approvalLevel === 'area' 
                ? '(Rejected by Area Manager) ' 
                : '(Rejected by Business Development Manager) ';

            $input->update([
                'status' => 'Butuh Revisi',
                'comment_review' => $commentPrefix . request('comment_review')
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Error fetching data in index: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menolak data.']);
        }
    }

    public function update(Request $request, $id)
{
    if (auth()->user()->role->role_name === 'Store Manager') {
        try {
            $storeInput = InputStore::findOrFail($id);

            $validated = $request->validate([
                'period' => 'required|date',
                'aksesibilitas' => 'required|integer|between:1,4',
                'visibilitas' => 'required|integer|between:1,4',
                'lingkungan' => 'required|array',
                'lingkungan.*' => 'integer|between:1,3',
                'lalu_lintas' => 'required|integer|min:0',
                'parkir_mobil' => 'required|integer|min:0',
                'parkir_motor' => 'required|integer|min:0',
                'comment_input' => 'nullable|string',
                'comment_review' => 'nullable|string',
            ]);

            // Ubah ini:
            // $validated['lingkungan'] = implode(',', $validated['lingkungan']);
            // Menjadi:
            $validated['lingkungan'] = json_encode($validated['lingkungan']);
            
            $validated['status'] = 'Sedang Direview Manager Area';

            $storeInput->update($validated);

            return redirect()->route('store.index')->with('success', 'Store evaluation updated and resubmitted for review');
        } catch (\Exception $e) {
            \Log::error('Error fetching data in index: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Gagal update data store.']);
        }
    }
    abort(403, 'Unauthorized action.');
}
}
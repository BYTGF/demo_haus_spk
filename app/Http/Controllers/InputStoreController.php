<?php

namespace App\Http\Controllers;

use App\Models\InputStore;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InputStoreController extends Controller
{
    public function index()
    {
        try {
            $user = auth()->user();

            $dones = InputStore::with('user', 'store')
                ->when($user->role->role_name === 'Store Manager', function ($query) use ($user) {
                    $query->where('store_id', $user->store_id);
                })
                ->when($user->role->role_name === 'Area Manager', function ($query) use ($user) {
                    $storeIds = Store::where('area_id', $user->area_id)->pluck('id');
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
        if (auth()->user()->role->role_name === 'Store Staff') {
            $validated = $request->validate([
                'period' => 'required|date',
                'aksesibilitas' => 'required|integer|between:1,4',
                'visibilitas' => 'required|integer|between:1,4',
                'lingkungan' => 'required|array',
                'lingkungan.*' => 'integer|between:1,3',
                'lalu_lintas' => 'required|integer|between:1,4',
                'parkir_mobil' => 'required|integer|min:0',
                'parkir_motor' => 'required|integer|min:0',
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

            
            $validated['user_id'] = auth()->id();
            $validated['status'] = 'Sedang Direview Manager Area';
            $validated['period'] = now()->format('Y-m-d');
            $validated['store_id'] = auth()->user()->store_id;

            InputStore::create($validated);

            return redirect()->route('store.index')
                ->with('success', 'Store evaluation submitted for area manager review');
        }

        abort(403, 'Unauthorized action.');
    }

    public function approveArea(InputStore $input)
    {
        if (auth()->user()->role->role_name === 'Area Manager') {
            $input->update([
                'status' => 'Sedang Direview Manager BD',
                'comment_review' => request('comment_review', 'Approved by Area Manager')
            ]);

            return redirect()->route('store.index')
                ->with('success', 'Store evaluation approved and forwarded to Business Development Manager');
        }

        abort(403, 'Unauthorized action.');
    }

    public function approveBd(InputStore $input)
    {
        if (auth()->user()->role->role_name === 'Business Development Manager') {
            $input->update([
                'status' => 'Selesai',
                'comment_review' => request('comment_review', 'Approved by Business Development Manager')
            ]);

            return redirect()->route('store.index')
                ->with('success', 'Store evaluation approved successfully');
        }

        abort(403, 'Unauthorized action.');
    }

    public function reject($id)
    {
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
    }

    public function update(Request $request, $id)
    {
        if (auth()->user()->role->role_name === 'Store Manager') {
            $storeInput = InputStore::findOrFail($id);
            
            $validated = $request->validate([
                'period' => 'required|date',
                'aksesibilitas' => 'required|integer|between:1,4',
                'visibilitas' => 'required|integer|between:1,4',
                'lingkungan' => 'required|array',
                'lingkungan.*' => 'integer|between:1,3',
                'lalu_lintas' => 'required|integer|between:1,4',
                'parkir_mobil' => 'required|integer|min:0',
                'parkir_motor' => 'required|integer|min:0',
                'rating' => 'required|integer|between:1,5',
                'store_id' => 'required|exists:stores,id',
                'comment_input' => 'nullable|string',
                'comment_review' => 'nullable|string',
            ]);

            // Convert lingkungan array to comma-separated string
            $validated['lingkungan'] = implode(',', $validated['lingkungan']);
            
            $validated['status'] = 'Sedang Direview Manager Area'; // Reset status when updated

            $storeInput->update($validated);

            return redirect()->route('store.index')
                ->with('success', 'Store evaluation updated and resubmitted for review');
        }

        abort(403, 'Unauthorized action.');
    }
}
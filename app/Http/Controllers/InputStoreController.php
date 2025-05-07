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
            $dones = InputStore::with('user', 'store')
                ->where('status', 'Selesai')
                ->paginate(10, ['*'], 'dones_page');
            
            // Show inputs that are either in review or need revision
            $inputs = InputStore::with('user', 'store')
                ->whereIn('status', ['Sedang Direview Manager Area', 'Sedang Direview Manager BD', 'Butuh Revisi'])
                ->paginate(10, ['*'], 'inputs_page');
                
            // $stores = Store::all();

            return view('store', compact('dones', 'inputs'));
        } catch (\Exception $e) {
            \Log::error('Error fetching data in index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load store input data.');
        }
    }

    public function store(Request $request)
    {
        if (auth()->user()->role->role_name === 'Store Manager') {
            $validated = $request->validate([
                'aksesibilitas' => 'required|integer|between:1,5',
                'visibilitas' => 'required|integer|between:1,5',
                'lingkungan' => 'required|integer|between:1,5',
                'lalu_lintas' => 'required|integer|between:1,5',
                'area_parkir' => 'required|integer|between:1,5',
                'rating' => 'required|integer|between:1,5',
                'comment_input' => 'required|string',
            ]);

            $validated['period'] = now()->year;
            $validated['status'] = 'Sedang Direview Manager Area';
            $validated['store_id'] = auth()->store_id();;
            $validated['user_id'] = auth()->id();

            InputStore::create($validated);

            return redirect()->route('store.index')
                ->with('success', 'Store input submitted for Area Manager approval');
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
                ->with('success', 'Store input approved and forwarded to Business Development Manager');
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
                ->with('success', 'Store input approved successfully');
        }

        abort(403, 'Unauthorized action.');
    }

    public function reject($id)
    {
        $input = InputStore::findOrFail($id);
        $approvalLevel = request('approval_level');
        
        request()->validate([
            'comment_review' => 'required|string'
        ]);

        $input->update([
            'status' => 'Butuh Revisi',
            'comment_review' => request('comment_review') . 
                ($approvalLevel === 'area' ? ' (Rejected by Area Manager)' : ' (Rejected by Business Development Manager)')
        ]);

        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id)
    {
        if (auth()->user()->role->role_name === 'Store Manager') {
            $storeInput = InputStore::findOrFail($id);
            
            $validated = $request->validate([
                'aksesibilitas' => 'required|integer|between:1,5',
                'visibilitas' => 'required|integer|between:1,5',
                'lingkungan' => 'required|integer|between:1,5',
                'lalu_lintas' => 'required|integer|between:1,5',
                'area_parkir' => 'required|integer|between:1,5',
                'rating' => 'required|integer|between:1,5',
                'comment_input' => 'required|string',
                'comment_review' => 'nullable|string',
                'store_id' => 'required|exists:stores,id',
            ]);

            // When store manager updates, reset status to first approval level
            $validated['status'] = 'Sedang Direview Manager Area';

            $storeInput->update($validated);

            return redirect()->route('store.index')
                ->with('success', 'Store input updated and resubmitted for approval.');
        }

        abort(403, 'Unauthorized action.');
    }
}
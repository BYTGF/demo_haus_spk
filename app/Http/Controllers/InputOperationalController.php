<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
// use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Store;
use App\Models\InputOperational;

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
                $query->where('store_id', $user->store_id);
            })
            ->where('status', 'Selesai')
            ->latest()
            ->paginate(10, ['*'], 'dones_page');
            
            $inputs = InputOperational::with('user', 'store')
            ->when($user->role->role_name === 'Operational', function ($query) use ($user) {
                // Staff Finance cuma lihat store miliknya sendiri
                $query->where('store_id', $user->store_id);
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

            $validated = $request->validate([
                'gaji_upah' => 'required|integer|min:0',
                'sewa' => 'required|integer|min:0',
                'utilitas' => 'required|integer|min:0',
                'perlengkapan' => 'required|integer|min:0',
                'lain_lain' => 'required|integer|min:0',
                // 'rating' => 'required|integer|between:1,5',
                // 'comment_input' => 'required|string',
            ]);

            // Calculate total
            $validated['total'] = 
                $validated['gaji_upah'] + 
                $validated['sewa'] + 
                $validated['utilitas'] + 
                $validated['perlengkapan'] + 
                $validated['lain_lain'];

            $validated['period'] = now()->format('Y-m-d');
            $validated['status'] = 'Sedang Direview';
            $validated['store_id'] = auth()->user()->store_id;
            $validated['user_id'] = auth()->id();

            InputOperational::create($validated);

            return redirect()->route('operational.index')
                ->with('success', 'Operational input submitted for approval');
        }

        abort(403, 'Unauthorized action.');
    }

    public function approve(InputOperational $input)
    {
        if (auth()->user()->role->role_name === 'Manager Business Development') {
            $input->update([
                'status' => 'Selesai',
                'comment_review' => request('comment_review', 'Approved')
            ]);

            

            return redirect()->route('operational.index')
                ->with('success', 'Operational input approved successfully');
        }

        abort(403, 'Unauthorized action.');
    }

    public function reject($id)
    {
        $input = InputOperational::findOrFail($id);
        
        request()->validate([
            'comment_review' => 'required|string'
        ]);

        $input->update([
            'status' => 'Butuh Revisi',
            'comment_review' => request('comment_review')
        ]);

        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id)
    {
        if (auth()->user()->role->role_name == 'Operational') {
            $operationalInput = InputOperational::findOrFail($id);
            
            $validated = $request->validate([
                'gaji_upah' => 'required|integer|min:0',
                'sewa' => 'required|integer|min:0',
                'utilitas' => 'required|integer|min:0',
                'perlengkapan' => 'required|integer|min:0',
                'lain_lain' => 'required|integer|min:0',
                'rating' => 'required|integer|between:1,5',
                'comment_input' => 'required|string',
                'comment_review' => 'nullable|string',
                'store_id' => 'required|exists:stores,id',
            ]);

            // Calculate total
            $validated['total'] = 
                $validated['gaji_upah'] + 
                $validated['sewa'] + 
                $validated['utilitas'] + 
                $validated['perlengkapan'] + 
                $validated['lain_lain'];

            $operationalInput->update($validated);

            return redirect()->route('operational.index')
                ->with('success', 'Operational input updated successfully.');
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

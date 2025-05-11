<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Store;
use App\Models\InputBD;

class InputBDController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $user = auth()->user();
            $stores = Store::all(); // Get all stores for the dropdown
            
            $dones = InputBD::with('user', 'store')
                ->when($user->role->role_name === 'Business Development', function ($query) use ($user) {
                    $query->where('user_id', $user->id); // Filter by user instead of store
                })
                ->where('status', 'Selesai')
                ->latest()
                ->paginate(10, ['*'], 'dones_page');
            
            $inputs = InputBD::with('user', 'store')
                ->when($user->role->role_name === 'Business Development', function ($query) use ($user) {
                    $query->where('user_id', $user->id); // Filter by user instead of store
                })
                ->whereIn('status', ['Sedang Direview', 'Butuh Revisi'])
                ->latest()
                ->paginate(10, ['*'], 'inputs_page');

                
            return view('bd', compact('dones', 'inputs', 'stores'));
        } catch (\Exception $e) {
            \Log::error('Error fetching data in index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load business development data.');
        }
    }

    public function show($id)
    {
        $input = InputBD::with('store')->find($id);

        if (!$input) {
            return response()->json([
                'error' => 'Input not found',
            ], 404);
        }

        return response()->json($input);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (auth()->user()->role->role_name == 'Business Development') {
            $validated = $request->validate([
                'store_id' => 'required|exists:stores,id',
                'direct_competition' => 'required|integer|min:0',
                'substitute_competition' => 'required|integer|min:0',
                'indirect_competition' => 'required|integer|min:0',
                'comment_input' => 'nullable|string',
            ]);

            $validated['period'] = now()->format('Y-m-d');
            $validated['status'] = 'Sedang Direview';
            $validated['user_id'] = auth()->id();

            $exists = InputBD::where('store_id', $request->store_id)
                ->where('period', $request->period)
                ->exists();

            if ($exists) {
                return redirect()->back()
                    ->withErrors(['period' => 'Kamu sudah input data untuk periode ini.'])
                    ->withInput();
            }

            InputBD::create($validated);

            return redirect()->route('bd.index')
                ->with('success', 'Business development input submitted for approval');
        }

        abort(403, 'Unauthorized action.');
    }

    /**
     * Approve the specified resource.
     */
    public function approve(InputBD $input)
    {
        if (auth()->user()->role->role_name === 'Manager Business Development') {
            $input->update([
                'status' => 'Selesai',
                'rating' => request('rating', null),
                'comment_review' => request('comment_review', 'Approved')
            ]);

            return redirect()->route('bd.index')
                ->with('success', 'Business development input approved successfully');
        }

        abort(403, 'Unauthorized action.');
    }

    /**
     * Reject the specified resource.
     */
    public function reject($id)
    {
        $input = InputBD::findOrFail($id);
        
        request()->validate([
            'comment_review' => 'required|string'
        ]);

        $input->update([
            'status' => 'Butuh Revisi',
            'comment_review' => request('comment_review')
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        if (auth()->user()->role->role_name == 'Business Development Staff') {
            $bdInput = InputBD::findOrFail($id);
            
            $validated = $request->validate([
                'direct_competition' => 'required|integer|min:0',
                'substitute_competition' => 'required|integer|min:0',
                'indirect_competition' => 'required|integer|min:0',
                'comment_input' => 'required|string',
                'comment_review' => 'nullable|string',
            ]);

            $bdInput->update($validated);

            return redirect()->route('bd.index')
                ->with('success', 'Business development input updated successfully.');
        }

        abort(403, 'Unauthorized action.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InputBD $inputBD)
    {
        //
    }
}
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
            $stores = Store::where('id', '!=', 1)->where('is_active', true)->get(); // Get all stores for the dropdown
            
            $dones = InputBD::with('user', 'store')
                ->when($user->role->role_name === 'Business Development', function ($query) use ($user) {
                    $query->where('user_id', $user->id)->where('is_active', true); // Filter by user instead of store
                })
                ->where('status', 'Selesai')
                ->latest()
                ->paginate(10, ['*'], 'dones_page');
            
            $inputs = InputBD::with('user', 'store')
                ->when($user->role->role_name === 'Business Development', function ($query) use ($user) {
                    $query->where('user_id', $user->id)->where('is_active', true); // Filter by user instead of store
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
        try {
            if (auth()->user()->role->role_name == 'Business Development Staff') {
                $validated = $request->validate([
                    'period' => 'required|date_format:Y-m',                    
                    'store_id' => 'required|exists:stores,id',
                    'direct_competition' => 'required|integer|min:0',
                    'substitute_competition' => 'required|integer|min:0',
                    'indirect_competition' => 'required|integer|min:0',
                    'comment_input' => 'nullable|string',
                ]);

                $validated['status'] = 'Sedang Direview';
                $validated['period'] = $validated['period'] . '-15'; // Format ke YYYY-MM-DD
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
                    ->with('success', 'Input business development berhasil dikirim untuk direview.');
            }

            abort(403, 'Unauthorized action.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function approve(InputBD $input)
    {
        try {
            if (auth()->user()->role->role_name === 'Manager Business Development') {
                $input->update([
                    'status' => 'Selesai',
                    'comment_review' => request('comment_review', 'Approved')
                ]);

                return redirect()->route('bd.index')
                    ->with('success', 'Input berhasil di-approve.');
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
            $input = InputBD::findOrFail($id);

            request()->validate([
                'comment_review' => 'required|string'
            ]);

            $input->update([
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
            if (auth()->user()->role->role_name == 'Business Development Staff') {
                $bdInput = InputBD::findOrFail($id);

                $validated = $request->validate([
                    'direct_competition' => 'required|integer|min:0',
                    'substitute_competition' => 'required|integer|min:0',
                    'indirect_competition' => 'required|integer|min:0',
                    'comment_input' => 'nullable|string',
                    'comment_review' => 'nullable|string',
                ]);

                $bdInput->update($validated);

                return redirect()->route('bd.index')
                    ->with('success', 'Input business development berhasil diupdate.');
            }

            abort(403, 'Unauthorized action.');
        } catch (\Throwable $e) {
            \Log::error('Error fetching data in index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal update data: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InputBD $inputBD)
    {
        //
    }
}
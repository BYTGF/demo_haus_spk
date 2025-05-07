<?php

namespace App\Http\Controllers;

use App\Models\InputBD;
use App\Http\Requests\StoreInputBDRequest;
use App\Http\Requests\UpdateInputBDRequest;

class InputBDController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $user = auth()->user();
            $dones = InputBD::with('user', 'store')
            ->when($user->role->role_name === 'Business Development Staff', function ($query) use ($user) {
                // Staff Finance cuma lihat store miliknya sendiri
                $query->where('store_id', $user->store_id);
            })
            ->where('status', 'Selesai')
            ->latest()
            ->paginate(10, ['*'], 'dones_page');
            
            $inputs = InputBD::with('user', 'store')
            ->when($user->role->role_name === 'Business Development Staff', function ($query) use ($user) {
                // Staff Finance cuma lihat store miliknya sendiri
                $query->where('store_id', $user->store_id);
            })
            ->whereIn('status', ['Sedang Direview', 'Butuh Revisi'])
            ->latest()
            ->paginate(10, ['*'], 'inputs_page');
                
            // $stores = Store::where('id', '!=', 1)->get();

            return view('BD', compact('dones', 'inputs'));
        } catch (\Exception $e) {
            \Log::error('Error fetching data in index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load operational data.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInputBDRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(InputBD $inputBD)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InputBD $inputBD)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInputBDRequest $request, InputBD $inputBD)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InputBD $inputBD)
    {
        //
    }
}

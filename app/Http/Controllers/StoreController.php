<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\Area;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index()
    {
        $stores = Store::with('area')->where('is_active', true)->paginate(10);
        $areas = Area::all();
        return view('manage-store', compact('stores', 'areas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'store_name' => 'required|string|max:255',
            'area_id' => 'required|exists:areas,id',
            'status' => 'required|string|in:active,inactive'
        ]);

        Store::create($request->only('store_name', 'area_id', 'status'));
        return redirect()->back()->with('success', 'Store berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'store_name' => 'required|string|max:255',
            'area_id' => 'required|exists:areas,id',
            'status' => 'required|string|in:active,inactive'
        ]);

        $store = Store::findOrFail($id);
        $store->update($request->only('store_name', 'area_id', 'status'));
        return redirect()->back()->with('success', 'Store berhasil diupdate.');
    }

    public function destroy($id)
    {
        $store = Store::findOrFail($id);
        $store->is_active = false;
        $store->save();

        return response()->json(['message' => 'Store berhasil dihapus.']);
    }
}

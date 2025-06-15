<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Area;

class AreaController extends Controller
{
    public function index()
    {
        $areas = Area::where('is_active', true)->paginate(10);
        return view('manage-area', compact('areas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'area_name' => 'required|string|max:255'
        ]);

        Area::create($request->only('area_name'));

        return redirect()->back()->with('success', 'Area berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'area_name' => 'required|string|max:255'
        ]);

        $area = Area::findOrFail($id);
        $area->update($request->only('area_name'));

        return redirect()->back()->with('success', 'Area berhasil diupdate.');
    }

    public function destroy($id)
    {
        $area = Area::findOrFail($id);
        $area->is_active = false;
        $area->save();

        return response()->json(['message' => 'Area berhasil dihapus.']);
    }
}

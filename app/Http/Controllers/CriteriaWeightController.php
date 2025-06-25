<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CriteriaWeight;
use Illuminate\Http\Request;

class CriteriaWeightController extends Controller
{
    public function index()
    {
        $weights = CriteriaWeight::where('is_active', true)->paginate(10);
        return view('manage-cw', compact('weights'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'criteria' => 'required|string',
            'weight' => 'required|numeric|min:0',
        ]);

        CriteriaWeight::create($request->all());
        return redirect()->back()->with('success', 'Bobot berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'criteria' => 'required|string',
            'weight' => 'required|numeric|min:0',
        ]);

        $data = CriteriaWeight::findOrFail($id);
        $data->update($request->all());

        return redirect()->back()->with('success', 'Bobot berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $cw = CriteriaWeight::findOrFail($id);
        $cw->is_active = false;
        $cw->save();
        return response()->json(['message' => 'Bobot berhasil dihapus.']);
    }
}

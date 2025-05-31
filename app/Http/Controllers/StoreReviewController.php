<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class StoreReviewController extends Controller
{
    public function index()
    {
        // pastikan hanya C-Level yang bisa
        if (auth()->user()->role->role_name !== 'C-Level') {
            abort(403, 'Akses ditolak.');
        }

        try {
            // ambil semua store yang aktif
            $stores = Store::where('is_active', true)->where('id', '!=', 1)->get()->map(function($store){
                // ambil rating terakhir per divisi
                $store->finance_rating     = $store->finances()
                    ->where('is_active', true)
                    ->orderByDesc('period')
                    ->value('rating');
                $store->operational_rating = $store->operationals()
                    ->where('is_active', true)
                    ->orderByDesc('period')
                    ->value('rating');
                $store->bd_rating          = $store->bds()
                    ->where('is_active', true)
                    ->orderByDesc('period')
                    ->value('rating');
                $store->store_rating       = $store->stores()
                    ->where('is_active', true)
                    ->orderByDesc('period')
                    ->value('rating');
                return $store;
            });

            return view('review-store', compact('stores'));

        } catch (\Throwable $e) {
            Log::error('StoreReview@index error: '.$e->getMessage());
            return back()->with('error', 'Gagal memuat data.');
        }
    }

    public function update(Request $request, Store $store)
    {
        if (auth()->user()->role->role_name !== 'C-Level') {
            abort(403, 'Akses ditolak.');
        }

        try {
            DB::transaction(function() use($store) {
                // non-aktifkan store
                $store->update(['is_active' => false]);
                // non-aktifkan semua input terkait
                $store->finances()->update(['is_active' => false]);
                $store->operationals()->update(['is_active' => false]);
                $store->bds()->update(['is_active' => false]);
                $store->stores()->update(['is_active' => false]);
            });

            return redirect()->route('review-store.index')
                             ->with('success', 'Store dan semua data terkait berhasil dinonaktifkan.');

        } catch (\Throwable $e) {
            Log::error('StoreReview@update error: '.$e->getMessage());
            return back()->with('error', 'Gagal menonaktifkan store.');
        }
    }
}

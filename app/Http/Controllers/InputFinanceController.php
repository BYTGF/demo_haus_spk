<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Store;
use App\Models\InputFinance;

class InputFinanceController extends Controller
{
    public function index()
    {
        try {
            // dd(Auth::check(), Auth::user());
            $dones = InputFinance::with('user','store')->where('status', 'selesai')->get();
            $reviews = InputFinance::with('user','store')->whereIn('status', ['Sedang Direview', 'Butuh Revisi'])->get();
            $stores = Store::where('id', '!=', 1)->get();
            // dd($done);

            return view('finance', compact('dones', 'reviews','stores'));
        } catch (\Exception $e) {
            // Log the error and return an error page or message
            \Log::error('Error fetching data in index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load finance data.');
        }
    }

    public function store(Request $request)
    {
        if (auth()->user()->role->role_name === 'Finance') {
            $validated = $request->validate([
                'neraca_keuangan' => 'required|integer|between:1,5',
                'arus_kas' => 'required|integer|between:1,5',
                'profitabilitas' => 'required|integer|between:1,5',
                'comment_input' => 'required|string',
                'store_id' => 'required|exists:stores,id',
            ]);

            $validated['status'] = 'Sedang Direview';
            $validated['user_id'] = auth()->id();

            InputFinance::create($validated);

            return redirect()->route('finance.index')
                ->with('success', 'Review submitted for approval');
        }

        abort(403, 'Unauthorized action.');
    
    }

    public function approve(InputFinance $review)
    {
        if (auth()->user()->role->role_name === 'Manager Business Development') {
            $review->update([
                'status' => 'Selesai',
                'comment_review' => request('comment_review', 'Approved')
            ]);

            dd($review);

            // return redirect()->route('finance.index')
            //     ->with('success', 'Review approved successfully');
        }

        abort(403, 'Unauthorized action.');
    }

    // FinanceController.php
    public function reject($id)
    {
        $review = InputFinance::findOrFail($id);
        
        request()->validate([
            'comment_review' => 'required|string'
        ]);

        $review->update([
            'status' => 'Butuh Revisi',
            'comment_review' => request('comment_review')
        ]);

        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id)
    {
        if (auth()->user()->role === 'Finance') {
            $financialReview = InputFinance::findOrFail($id);
            $validated = $request->validate([
                'neraca_keuangan' => 'required|integer|between:1,5',
                'arus_kas' => 'required|integer|between:1,5',
                'profitabilitas' => 'required|integer|between:1,5',
                'comment_input' => 'nullable|string',
                'comment_review' => 'required_if:status,Sedang Direview,Butuh Revisi|string|nullable',
                'status' => 'required|in:Sedang Direview,Butuh Revisi,Selesai',
                'store_id' => 'required|exists:stores,id',
            ]);
        
            $financialReview->update($validated);
        
            return redirect()->route('finance.index')->with('success', 'Financial review updated successfully.');
        }

        abort(403, 'Unauthorized action.');
    }

    public function destroy($id)
    {
        // try {
        //     $inputFinance = InputFinance::findOrFail($id);
        //     $inputFinance->delete();

        //     return redirect()->back()->with('success', 'User berhasil dihapus.');
        // } catch (\Exception $e) {
        //     \Log::error('Error in destroy: ' . $e->getMessage());
        //     return redirect()->back()->with('error', 'Failed to delete user.');
        // }
    }
}

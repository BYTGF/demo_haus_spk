<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Area;
use App\Models\Store;

class UserManagementController extends Controller
{
    public function index()
    {
        try {
            $userManagement = User::with(['role', 'area', 'store'])->latest()->paginate(10, ['*'], 'dones');
            $roles = Role::all();
            $areas = Area::all();
            $stores = Store::where('id', '!=', 1)->where('is_active', true)->get();

            return view('user-management', compact('userManagement', 'roles', 'areas', 'stores'));
        } catch (\Exception $e) {
            // Log the error and return an error page or message
            \Log::error('Error fetching data in index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load user management data.');
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'username' => 'required|unique:Users,username'
            ]);

        User::create([
            'username' => $request->username,
            'password' => Hash::make('password'),
            'role_id' => $request->role_id,
            'area_id' => $request->area_id,
            'store_id' => $request->store_id,
        ]);

        return redirect()->back()->with('success', 'Created New User.');
        } catch (\Exception $e) {
            \Log::error('Error in store: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to add user.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $request->validate([
                'username' => 'required|unique:users,username,' . ($user->id ?? ''),
                'password' => 'nullable|min:6',
                'role_id' => 'required|exists:roles,id',
                'area_id' => 'required|exists:areas,id',
                'store_id' => 'required|exists:stores,id',
            ]);

            $data = [
                'username' => $request->username,
                'role_id' => $request->role_id,
                'area_id' => $request->area_id,
                'store_id' => $request->store_id,
            ];

            if ($request->password) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);

            return redirect()->back()->with('success', 'User berhasil diupdate!');
        } catch (\Exception $e) {
            \Log::error('Error in update: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update user.');
        }
    }

    public function destroy($id)
    {
        try {
            $users = User::findOrFail($id);
            $users->delete();

            return redirect()->back()->with('success', 'User berhasil dihapus.');
        } catch (\Exception $e) {
            \Log::error('Error in destroy: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete user.');
        }
    }

}

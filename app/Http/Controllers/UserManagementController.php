<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserManagementController extends Controller
{
    public function index()
    {
        $userManagement = User::with(['role','area', 'store'])->latest()->get();
        return view('user-management', compact('userManagement'));
    }
}

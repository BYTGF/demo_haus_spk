<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class SessionsController extends Controller
{
    public function create()
    {
        return view('session.login-session');
    }

    public function store()
    {
        $attributes = request()->validate([
            'username'=>'required',
            'password'=>'required' 
        ]);

        if(Auth::attempt($attributes))
        {
            session()->regenerate();
            $user = Auth::user()->load(['role', 'area', 'store']);
            $user = Auth::user();

            // Redirect directly to dashboard, not home
            return redirect()->route('dashboard')->with(['success'=>'You are logged in.']);
        }
        else{
            return back()->withErrors(['username'=>'Username or password invalid.']);
        }
    }
    
    public function destroy()
    {

        Auth::logout();

        return redirect('/login')->with(['success'=>'You\'ve been logged out.']);
    }
}

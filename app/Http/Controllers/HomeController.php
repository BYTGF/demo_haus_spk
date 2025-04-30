<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home()
    {
        // Check if user is authenticated
        if (auth()->check()) {
            return redirect()->route('dashboard.index');
        }
        return redirect('/login');
    }
}

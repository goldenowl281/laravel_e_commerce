<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
        // $admin_name = Auth::guard('admin')->user()->name;
        // echo 'Welcome home, ' . $admin_name . '<br>';
        // echo '<a href="' . route('admin.logout') . '">Logout</a>';
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}

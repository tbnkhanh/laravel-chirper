<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        if (Auth::id()) {
            $userType = Auth()->user()->usertype;
            if ($userType == 'user') {
                return view('dashboard');
            } else if ($userType == 'admin') {
                return view('admin.admin');
            } else {
                return redirect()->back();
            }
        }
    }
}

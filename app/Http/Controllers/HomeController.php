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
            $userType = Auth()->user()->user_type;
            if ($userType == 'viewer') {
                return view('viewer.dashboard');
            } else if ($userType == 'admin') {
                return view('admin.dashboard');
            } else if ($userType == 'player') {
                return view('player.dashboard');
            } else {
                return redirect()->back();
            }
        }
    }
}

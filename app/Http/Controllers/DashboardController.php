<?php

namespace App\Http\Controllers;
use Illuminate\Routing\Controller; // Make sure you're importing the correct Controller class
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        switch ($user->role->role_name) {
            case 'super-admin':
                return redirect()->route('dashboard');
            case 'director-admin':
                return redirect()->route('dashboard');
            case 'divisional-user':
                return redirect()->route('dashboard');
            case 'district-user':
                return redirect()->route('dashboard');
            case 'committe-user':
                return redirect()->route('dashboard');
            default:
                abort(403, 'Unauthorized');
        }
    }
}
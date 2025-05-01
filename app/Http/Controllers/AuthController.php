<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\UserRole;

class AuthController extends Controller
{
    // Show login form (Web)
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Handle login (Web or API)
    public function login(Request $request)
    {
        $isApi = $request->expectsJson(); // Detect if request is API or web

        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $credentials['username'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            if ($isApi) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }
            return back()->withErrors(['login' => 'Invalid username or password']);
        }

        if ($isApi) {
            $token = $user->createToken('APIToken')->plainTextToken;
            return response()->json([
                'message' => 'Login successful',
                'token' => $token,
            ]);
        } else {
            Auth::login($user);
            return redirect()->route('home'); // Home route handles redirection by role
        }
    }

    // Logout (Web + API)
    public function logout(Request $request)
    {
        if ($request->expectsJson()) {
            $request->user()->currentAccessToken()->delete();
            return response()->json(['message' => 'Logged out successfully']);
        }
        Auth::logout();
        return redirect()->route('login');
    }
}

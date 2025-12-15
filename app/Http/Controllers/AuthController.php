<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Redirect based on role (new structure)
            $user = Auth::user();
            return match ($user->role) {
                'staff' => redirect()->intended('/staff/dashboard'),
                'owner' => redirect()->intended('/owner/dashboard'),
                'super_admin' => redirect()->intended('/super/dashboard'),
                default => redirect()->intended('/home'), // customers
            };
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:20',
            'unit_no' => 'nullable|string|max:50',
            'block' => 'nullable|string|max:50',
        ]);

        // Get or create default apartment (for MVP single tenant)
        $apartment = \App\Models\Apartment::first();
        if (!$apartment) {
            $apartment = \App\Models\Apartment::create([
                'name' => "D'house Waffle",
                'address' => 'Default Location',
                'service_fee_percent' => 0.00,
                'pickup_location' => 'Lobby Utama',
                'pickup_start_time' => '09:00:00',
                'pickup_end_time' => '21:00:00',
                'status' => 'active',
            ]);
        }

        $user = User::create([
            'apartment_id' => $apartment->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'],
            'unit_no' => $validated['unit_no'],
            'block' => $validated['block'],
            'role' => 'customer',
            'status' => 'active',
        ]);

        Auth::login($user);

        return redirect('/home');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}

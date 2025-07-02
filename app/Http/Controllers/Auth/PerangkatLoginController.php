<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\Linmas;

class PerangkatLoginController extends Controller
{
    /**
     * Show the perangkat login form.
     */
    public function showLoginForm()
    {
        return view('auth.perangkat-login');
    }

    /**
     * Handle a login request to the application.
     */
    public function login(Request $request)
    {
        $request->validate([
            'nik' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Check if linmas exists and can login
        $linmas = Linmas::where('nik', $request->nik)
                        ->where('can_login', true)
                        ->first();

        if (!$linmas) {
            throw ValidationException::withMessages([
                'nik' => ['NIK tidak terdaftar atau tidak memiliki akses login.'],
            ]);
        }

        // Attempt to log the user in
        if (Auth::guard('perangkat')->attempt([
            'nik' => $request->nik,
            'password' => $request->password,
            'can_login' => true
        ], $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('perangkat.dashboard'));
        }

        throw ValidationException::withMessages([
            'nik' => ['NIK atau password salah.'],
        ]);
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::guard('perangkat')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('perangkat.login');
    }
}
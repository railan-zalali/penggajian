<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsurePerangkatIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $linmas = Auth::guard('perangkat')->user();

        if (!$linmas || !$linmas->can_login) {
            Auth::guard('perangkat')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('perangkat.login')
                ->withErrors(['error' => 'Akses ditolak atau sesi Anda telah berakhir. Silakan login kembali.']);
        }

        return $next($request);
    }
}

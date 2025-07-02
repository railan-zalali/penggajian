<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureLinmasCanLogin
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
            return redirect()->route('perangkat.login')
                ->withErrors(['error' => 'Akses ditolak. Akun Anda tidak memiliki izin login atau telah dinonaktifkan.']);
        }
        
        return $next($request);
    }
}
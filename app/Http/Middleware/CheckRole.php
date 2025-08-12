<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user()) {
            // Jika user belum login, redirect ke halaman login
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if (!$request->user()->hasRole($role)) {
            // Log akses tidak sah
            Log::warning('Unauthorized access attempt', [
                'user_id' => $request->user()->id,
                'user_name' => $request->user()->name,
                'required_role' => $role,
                'user_role' => $request->user()->role,
                'route' => $request->route()->getName(),
                'url' => $request->fullUrl()
            ]);

            // Jika request adalah AJAX, kembalikan response JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'Anda tidak memiliki izin untuk mengakses fitur ini.'], 403);
            }

            // Redirect dengan pesan error
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk mengakses fitur ini.');
        }

        return $next($request);
    }
}

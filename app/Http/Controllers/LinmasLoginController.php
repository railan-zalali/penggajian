<?php

namespace App\Http\Controllers;

use App\Models\Linmas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class LinmasLoginController extends Controller
{
    /**
     * Display a listing of linmas with login access.
     */
    public function index()
    {
        $linmasWithLogin = Linmas::where('can_login', true)
            ->orderBy('nama')
            ->paginate(15);
            
        $linmasWithoutLogin = Linmas::where('can_login', false)
            ->orWhereNull('can_login')
            ->orderBy('nama')
            ->paginate(15);
            
        return view('admin.linmas-login.index', compact('linmasWithLogin', 'linmasWithoutLogin'));
    }

    /**
     * Show the form for creating login access for linmas.
     */
    public function create(Linmas $linmas)
    {
        if ($linmas->can_login) {
            return redirect()->route('admin.linmas-login.index')
                ->with('error', 'Linmas ini sudah memiliki akses login.');
        }
        
        return view('admin.linmas-login.create', compact('linmas'));
    }

    /**
     * Store login access for linmas.
     */
    public function store(Request $request, Linmas $linmas)
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:linmas,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $linmas->update([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'can_login' => true,
        ]);

        return redirect()->route('admin.linmas-login.index')
            ->with('success', 'Akses login berhasil diberikan kepada ' . $linmas->nama);
    }

    /**
     * Show the form for editing login access.
     */
    public function edit(Linmas $linmas)
    {
        if (!$linmas->can_login) {
            return redirect()->route('admin.linmas-login.index')
                ->with('error', 'Linmas ini tidak memiliki akses login.');
        }
        
        return view('admin.linmas-login.edit', compact('linmas'));
    }

    /**
     * Update login access for linmas.
     */
    public function update(Request $request, Linmas $linmas)
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:linmas,email,' . $linmas->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $updateData = [
            'email' => $request->email,
        ];
        
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $linmas->update($updateData);

        return redirect()->route('admin.linmas-login.index')
            ->with('success', 'Data login berhasil diperbarui untuk ' . $linmas->nama);
    }

    /**
     * Remove login access from linmas.
     */
    public function destroy(Linmas $linmas)
    {
        $linmas->update([
            'email' => null,
            'password' => null,
            'can_login' => false,
        ]);

        return redirect()->route('admin.linmas-login.index')
            ->with('success', 'Akses login berhasil dicabut dari ' . $linmas->nama);
    }
    
    /**
     * Toggle login access for linmas.
     */
    public function toggle(Linmas $linmas)
    {
        $linmas->update([
            'can_login' => !$linmas->can_login
        ]);
        
        $status = $linmas->can_login ? 'diaktifkan' : 'dinonaktifkan';
        
        return redirect()->route('admin.linmas-login.index')
            ->with('success', 'Akses login ' . $linmas->nama . ' berhasil ' . $status);
    }
}
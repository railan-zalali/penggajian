<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Linmas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::with('linmas')->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $linmasOptions = Linmas::whereDoesntHave('user')->get();
        return view('users.create', compact('linmasOptions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:admin,perangkat_desa'],
            'linmas_id' => ['nullable', 'required_if:role,perangkat_desa', 'exists:linmas,id']
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'linmas_id' => $request->role === 'perangkat_desa' ? $request->linmas_id : null
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $linmasOptions = Linmas::whereDoesntHave('user')
            ->orWhereHas('user', function ($query) use ($user) {
                $query->where('id', $user->id);
            })
            ->get();
        return view('users.edit', compact('user', 'linmasOptions'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'in:admin,perangkat_desa'],
            'linmas_id' => ['nullable', 'required_if:role,perangkat_desa', 'exists:linmas,id']
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'linmas_id' => $request->role === 'perangkat_desa' ? $request->linmas_id : null
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', Rules\Password::defaults()],
            ]);
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}

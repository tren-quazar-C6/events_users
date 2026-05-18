<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /** GET /dashboard/profile */
    public function show()
    {
        return view('dashboard.profile');
    }

    /** PATCH /dashboard/profile — actualiza nombre y email */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            // 'unique' con ->ignore: el email debe ser único EXCEPTO si es el del propio usuario.
            // Sin el ignore, el form fallaría al guardar el mismo email actual.
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->update($validated);

        return back()->with('status', 'Perfil actualizado correctamente.');
    }

    /** PATCH /dashboard/profile/password — cambia la contraseña */
    public function updatePassword(Request $request)
    {
        $request->validate([
            // 'current_password' es una regla built-in: valida contra el password
            // del usuario logueado actualmente. Magia de Laravel.
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('status', 'Contraseña actualizada correctamente.');
    }
}
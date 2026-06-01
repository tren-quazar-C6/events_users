<?php

namespace App\Http\Controllers;

use App\Services\UsuarioSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function __construct(
        private readonly UsuarioSyncService $usuarioSyncService,
    ) {
    }

    /** GET /dashboard/profile */
    public function show()
    {
        return view('dashboard.profile');
    }

    /** PATCH /dashboard/profile — actualiza nombre y email */
    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $request->user()->update($validated);
        $this->usuarioSyncService->sync($request->user());

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
        $this->usuarioSyncService->sync($request->user());

        return back()->with('status', 'Contraseña actualizada correctamente.');
    }
}

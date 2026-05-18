@extends('layouts.dashboard')

@section('title', 'Mi perfil')

@section('dashboard-content')
    @php $user = auth()->user(); @endphp

    <h1 class="font-display text-4xl text-sage-dark mb-6">Mi perfil</h1>

    {{-- Formulario: datos personales --}}
    <div class="bg-white rounded-card shadow-soft p-6 mb-6">
        <h2 class="font-display text-2xl text-sage-dark mb-4">Datos personales</h2>

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label for="name" class="block text-sm font-semibold mb-2">Nombre completo</label>
                <input id="name" type="text" name="name"
                       value="{{ old('name', $user->name) }}"
                       required
                       class="w-full px-4 py-3 rounded-btn border border-sage-dark/20 bg-white focus:outline-none focus:ring-2 focus:ring-sage/20 focus:border-sage transition" />
                @error('name') <p class="mt-1 text-sm text-coral">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-semibold mb-2">Correo electrónico</label>
                <input id="email" type="email" name="email"
                       value="{{ old('email', $user->email) }}"
                       required
                       class="w-full px-4 py-3 rounded-btn border border-sage-dark/20 bg-white focus:outline-none focus:ring-2 focus:ring-sage/20 focus:border-sage transition" />
                @error('email') <p class="mt-1 text-sm text-coral">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="bg-sage text-white font-semibold px-6 py-3 rounded-btn hover:bg-sage-dark transition">
                Guardar cambios
            </button>
        </form>
    </div>

    {{-- Formulario: contraseña --}}
    <div class="bg-white rounded-card shadow-soft p-6">
        <h2 class="font-display text-2xl text-sage-dark mb-4">Cambiar contraseña</h2>

        <form method="POST" action="{{ route('profile.password.update') }}" class="space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label for="current_password" class="block text-sm font-semibold mb-2">Contraseña actual</label>
                <input id="current_password" type="password" name="current_password" required
                       class="w-full px-4 py-3 rounded-btn border border-sage-dark/20 bg-white focus:outline-none focus:ring-2 focus:ring-sage/20 focus:border-sage transition" />
                @error('current_password') <p class="mt-1 text-sm text-coral">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold mb-2">Nueva contraseña</label>
                <input id="password" type="password" name="password" required
                       class="w-full px-4 py-3 rounded-btn border border-sage-dark/20 bg-white focus:outline-none focus:ring-2 focus:ring-sage/20 focus:border-sage transition" />
                <p class="mt-1 text-xs text-sage-dark/60">Mínimo 8 caracteres.</p>
                @error('password') <p class="mt-1 text-sm text-coral">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-semibold mb-2">Confirmar nueva contraseña</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                       class="w-full px-4 py-3 rounded-btn border border-sage-dark/20 bg-white focus:outline-none focus:ring-2 focus:ring-sage/20 focus:border-sage transition" />
            </div>

            <button type="submit" class="bg-sage text-white font-semibold px-6 py-3 rounded-btn hover:bg-sage-dark transition">
                Actualizar contraseña
            </button>
        </form>
    </div>
@endsection
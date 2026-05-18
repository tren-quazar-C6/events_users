<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /** GET /login — muestra el formulario */
    public function login()
    {
        return view('auth.login');
    }

    /** POST /login — procesa el intento de login */
    public function auth(Request $request)
    {
        // 1. VALIDACIÓN
        //    Si algo falla, Laravel redirige solito al form con los errores
        //    accesibles vía la variable $errors en la vista.
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. INTENTO DE AUTENTICACIÓN
        //    Auth::attempt busca un User con ese email, compara el password
        //    contra el hash en BD (con Hash::check internamente), y si todo
        //    encaja llama a Auth::login() para crear la sesión.
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // 3. REGENERAR SESIÓN
            //    Seguridad: previene el ataque "session fixation".
            //    Genera un session ID nuevo después del login exitoso.
            $request->session()->regenerate();

            // 4. REDIRECT
            //    Si el usuario quería ir a /dashboard pero el middleware 'auth'
            //    lo mandó al login, Laravel guardó esa URL. intended() lo manda
            //    de vuelta. Si no había URL guardada, va al default.
            return redirect()->intended(route('dashboard'));
        }

        // 5. SI FALLA: volver con error y conservar el email tipeado
        return back()
            ->withErrors(['email' => 'Las credenciales no coinciden.'])
            ->onlyInput('email');
    }

    /** GET /register — muestra el formulario de registro */
    public function showRegister()
    {
        return view('auth.register');
    }

    /** POST /register — crea el usuario y lo loguea automáticamente */
    public function register(Request $request)
    {
        // 1. VALIDACIÓN
        //    - email tiene que ser único en la tabla users
        //    - 'confirmed' busca un campo password_confirmation en el form
        //      y verifica que sea idéntico a password. Súper conveniente.
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // 2. CREAR USUARIO
        //    Hash::make encripta la contraseña con bcrypt antes de guardarla.
        //    NUNCA guardes contraseñas en texto plano.
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // 3. AUTO-LOGIN tras el registro (UX: no obligamos a loguearse de nuevo)
        Auth::login($user);
        $request->session()->regenerate();

        // 4. REDIRECT al dashboard
        return redirect()->intended(route('dashboard'));
    }

    /** POST /logout — cierra la sesión */
    public function logout(Request $request)
    {
        Auth::logout();                              // limpia el guard
        $request->session()->invalidate();           // destruye la data de sesión
        $request->session()->regenerateToken();      // nuevo token CSRF

        return redirect('/');
    }
}

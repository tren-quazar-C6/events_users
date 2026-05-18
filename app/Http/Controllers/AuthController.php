<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            'email'    => ['required', 'email'],
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

    /** POST /logout — cierra la sesión */
    public function logout(Request $request)
    {
        Auth::logout();                              // limpia el guard
        $request->session()->invalidate();           // destruye la data de sesión
        $request->session()->regenerateToken();      // nuevo token CSRF

        return redirect('/');
    }
}
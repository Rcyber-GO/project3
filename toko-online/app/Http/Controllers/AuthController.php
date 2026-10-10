<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['username' => 'Username atau password salah.'])
                ->onlyInput('username');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('products.index'));
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username'],
            'no_hp' => ['required', 'string', 'max:15'],
            'alamat' => ['required', 'string', 'max:2000'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::create([
            'id_user' => 'U'.Str::upper(Str::random(14)),
            ...$validated,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('products.index');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('products.index');
    }
}

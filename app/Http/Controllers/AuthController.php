<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\AuthLoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(AuthLoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials, true)) {
            $request->session()->regenerate();
            $request->session()->flash('success', 'Login successful!');

            return redirect()->intended(route('home'));
        }

        return redirect()->back()->withErrors(['username' => 'Incorrect username or password.']);
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();

        return redirect()->route('auth.login');
    }
}

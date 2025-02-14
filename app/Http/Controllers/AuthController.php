<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials, true)) {
            $request->session()->regenerate();
            $request->session()->flash('success', 'Login successful!');

            $user = auth()->user();

            if ($user->hasRole('customer')) {
                return redirect()->intended(route('home'));
            } else {
                return redirect()->intended(route('admin.home'));
            }
        }

        return redirect()->back()->withErrors(['username' => 'Incorrect username or password.']);
    }


    public function logout(): RedirectResponse
    {
        Auth::logout();

        return redirect()->route('auth.login');
    }
}

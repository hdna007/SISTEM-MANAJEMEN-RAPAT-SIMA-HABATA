<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
{
    $credentials = $request->validate([
        'username' => 'required',
        'password' => 'required'
    ]);

    if (Auth::attempt($credentials)) {

        $request->session()->regenerate();

        $role = Auth::user()->role;

        if ($role === 'admin') {
            return redirect('/admin');
        }

        if ($role === 'proktor') {
            return redirect('/proktor');
        }

        if ($role === 'sekretaris') {
            return redirect('/approver');
        }

        if ($role === 'notulen') {
            return redirect('/notulen');
        }

        return redirect('/dashboard'); // default user
    }

    return back()->with('loginError', 'Login gagal!');
}

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}

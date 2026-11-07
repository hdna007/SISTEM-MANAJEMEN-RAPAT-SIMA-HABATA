<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Client\Events\RequestSending;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function index(){
        return view('auth.register',[
            'title' => 'Register',
            'active' => 'register'
        ]);
    }

    public function store(Request $request){

        $validatedData = $request->validate([
            'nama_lengkap' => 'required|max:255',
            'username' => 'required|min:3|max:255|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:5|max:255|confirmed'
        ]);

        $validatedData['password'] = Hash::make($validatedData['password']);

        User::create($validatedData);

        return redirect('/login')->with('success', 'Registrasi berhasil, silahkan login!');
    }
}

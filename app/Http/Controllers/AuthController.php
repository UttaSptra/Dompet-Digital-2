<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm(Request $request)
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request ->validate([
            'email'=> 'required|email',
            'password'=> 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))){
            return redirect('dashboard')->with('success', 'Login Berhasil!');
        }
        return back()->withErrors(['email'=> 'Email atau Password salah']);
    }

    public function logout(Request $request)                
    {
        Auth::logout();
        return redirect()->route('login')->with('success', 'Logout Berhasil');
    }
}

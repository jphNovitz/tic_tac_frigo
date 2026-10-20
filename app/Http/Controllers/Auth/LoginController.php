<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
   public function showLoginForm(){
       return view('auth.login');
   }

   public function login(Request $request){
       $request->validate([
          'email' => 'required|email',
          'password' => 'required|string',
       ]);

       $credentials = $request->only('email', 'password');

       if (auth()->attempt($credentials)) {
           $request->session()->regenerate();
           return redirect()->intended(route('dashboard'));
       }

       return back()->withErrors([
           'email' => __('auth.failed'),
       ])->onlyInput('email');
   }

   public function logout(Request $request){
       auth()->logout();
       $request->session()->invalidate();
       $request->session()->regenerateToken();
       return redirect()->route('login');
   }
}

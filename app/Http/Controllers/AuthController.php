<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showCustomerLogin()
    {
        return view('auth.customer-login');
    }

    public function customerLoginSubmit(Request $request)
    {
        // TODO: real auth logic goes here later
        return back()->with('message', 'Login submitted (not yet connected to real auth)');
    }

    public function showCustomerRegister()
    {
        return view('auth.customer-register');
    }

    public function customerRegisterSubmit(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:30',
            'last_name' => 'nullable|string|max:30',
            'phone_number' => 'required|string|max:20',
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        // TODO: real registration logic goes here later (hash password,
        // insert into `user` table, log them in).
        return back()->with('message', 'Registration submitted (not yet connected to real auth)');
    }

    public function showCustomerForgotPassword()
    {
        return view('auth.customer-forgot-password');
    }

    public function customerForgotPasswordSubmit(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // TODO: Add the real password reset feature later.
        // It will send a reset email if the account exists.
        // For security, always show the same success message.
        return back()->with('message', 'If that email exists in our system, a reset link has been sent.');
    }
}

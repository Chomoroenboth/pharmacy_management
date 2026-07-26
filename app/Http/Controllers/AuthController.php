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

    public function showAdminLogin()
    {
        return view('auth.admin-login');
    }

    public function adminLoginSubmit(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
            'password' => 'required|string',
        ]);

        // TODO: real auth logic goes here later (check against `staff` table)
        return back()->with('message', 'Staff login submitted (not yet connected to real auth)');
    }

    public function showAdminForgotPassword()
    {
        return view('auth.admin-forgot-password');
    }

    public function adminForgotPasswordSubmit(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
        ]);

        // TODO: real password-reset logic goes here later.
        // Same "always show success" pattern as the customer side.
        return back()->with('message', 'If that email exists in our system, a reset link has been sent.');
    }

    public function showAdminResetPassword()
    {
        return view('auth.admin-reset-password');
    }

    public function adminResetPasswordSubmit(Request $request)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        // TODO: real password-update logic goes here later
        // (requires a valid reset token to identify which staff account).
        return back()->with('message', 'Password updated (not yet connected to real auth)');
    }
}

    
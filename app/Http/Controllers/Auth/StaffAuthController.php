<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffAuthController extends Controller
{
    public function staffLogin(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        $staff = Staff::where('email', $request->email)->first();

        if (!$staff || !Hash::check($request->password, $staff->password)) {
            return response()->json(['error' => 'Invalid staff credentials'], 401);
        }

        $token = $staff->createToken('staff_auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Staff login successful',
            'staff'   => $staff,
            'token'   => $token
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Staff successfully logged out'
        ], 200);
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserAuthController extends Controller
{
    public function register(Request $request)
    {
        $exists = DB::table('users')->where('email', $request->email)->exists();
        if ($exists) {
            return response()->json(['error' => 'Email already exists'], 400);
        }

        $userId = DB::table('users')->insertGetId([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone_number,
            'date_of_birth' => $request->date_of_birth,
            'address' => $request->address
        ]);

        return response()->json(['message' => 'Account created successfully', 'user_id' => $userId], 201);
    }

    public function logout(Request $request)
{
    // Revoke the token that was used to authenticate the current request
    $request->user()->currentAccessToken()->delete();

    return response()->json([
        'message' => 'Successfully logged out'
    ], 200);
}

    public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    $user = User::where('email', $request->email)->first();

    // Check if user exists and password is correct
    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json([
            'message' => 'Invalid credentials'
        ], 401);
    }

    // Generate the Sanctum Token
    $token = $user->createToken('auth_token')->plainTextToken;

    // Return the token in the response
    return response()->json([
        'message' => 'Login successful',
        'user' => $user,
        'token' => $token
    ]);

}
    public function resetPassword(Request $request)
{
    $request->validate([
        'email'    => 'required|email|exists:users,email',
        'password' => 'required|string|min:6|confirmed',
    ]);

    DB::table('users')
        ->where('email', $request->email)
        ->update(['password' => Hash::make($request->password)]);

    return response()->json([
        'status'  => 'success',
        'message' => 'Password reset successfully.'
    ]);
}
}

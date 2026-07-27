<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    // GET /profile
    public function show(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'data' => $request->user()
        ], 200);
    }

    // PUT /profile
    public function update(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $userId = $user->user_id ?? $user->id;

        DB::table('users')->where('user_id', $userId)->update(
            $request->only(['first_name', 'last_name', 'phone_number', 'address'])
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Profile updated'
        ]);
    }
}   

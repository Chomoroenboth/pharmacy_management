<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AllergyController extends Controller
{
    // GET /profile/allergies
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $userId = $user->user_id ?? $user->id;

        $allergies = DB::table('allergies')->where('user_id', $userId)->get();

        return response()->json([
            'status' => 'success',
            'data'   => $allergies
        ]);
    }

    // POST /profile/allergies
    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $userId = $user->user_id ?? $user->id;

        $request->validate([
            'allergy_name' => 'required|string|max:100',
        ]);

        $allergyId = DB::table('allergies')->insertGetId([
            'user_id'      => $userId,
            'allergy_name' => $request->allergy_name
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Allergy added',
            'data'    => ['allergy_id' => $allergyId]
        ], 201);
    }

    // DELETE /profile/allergies/{id}
    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $userId = $user->user_id ?? $user->id;

        // Ensure the allergy belongs to the authenticated user before deleting
        $allergy = DB::table('allergies')
            ->where('allergy_id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$allergy) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Allergy not found.'
            ], 404);
        }

        DB::table('allergies')->where('allergy_id', $id)->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Allergy deleted'
        ]);
    }
}

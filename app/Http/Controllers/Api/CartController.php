<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function store(Request $request)
    {
        // Get the authenticated user
        $user = $request->user();

        // Safety check if user is not logged in
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Get user_id (supports both 'user_id' and default 'id')
        $userId = $user->user_id ?? $user->id;

        DB::table('carts')->insert([
            'user_id'     => $userId,
            'medicine_id' => $request->medicine_id,
            'quantity'    => $request->quantity ?? 1,
            'added_at'    => now()
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Item added to cart successfully'
        ], 201);
    }

    public function update(Request $request, $id)
    {
        // 0. Get the authenticated user
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $userId = $user->user_id ?? $user->id;

        // 1. Validate the incoming data (must have a quantity of at least 1)
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        // 2. Find the specific cart item using the REAL primary key: cart_id
        $cartItem = Cart::where('cart_id', $id)
                        ->where('user_id', $userId)
                        ->first();

        // 3. If the item isn't in their cart, return an error
        if (!$cartItem) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Cart item not found.'
            ], 404);
        }

        // 4. Update the quantity and save to the database
        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        // 5. Return a success message
        return response()->json([
            'status'  => 'success',
            'message' => 'Cart quantity updated successfully.',
            'data'    => $cartItem
        ], 200);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    // GET /cart
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $userId = $user->user_id ?? $user->id;

        $items = DB::table('carts as c')
            ->join('medicines as m', 'c.medicine_id', '=', 'm.medicine_id')
            ->where('c.user_id', $userId)
            ->select(
                'c.cart_id',
                'c.medicine_id',
                'm.medicine_name',
                'm.price',
                'c.quantity',
                DB::raw('m.price * c.quantity as subtotal'),
                'c.added_at'
            )
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => $items
        ]);
    }

    // POST /cart
    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $userId = $user->user_id ?? $user->id;

        $request->validate([
            'medicine_id' => 'required|integer|exists:medicines,medicine_id',
            'quantity'    => 'nullable|integer|min:1',
        ]);

        $quantity = $request->quantity ?? 1;

        // If this medicine is already in the cart, bump the quantity instead of duplicating the row
        $existing = Cart::where('user_id', $userId)
            ->where('medicine_id', $request->medicine_id)
            ->first();

        if ($existing) {
            $existing->quantity += $quantity;
            $existing->save();

            return response()->json([
                'status'  => 'success',
                'message' => 'Cart quantity updated',
                'data'    => $existing
            ], 200);
        }

        $cartId = DB::table('carts')->insertGetId([
            'user_id'     => $userId,
            'medicine_id' => $request->medicine_id,
            'quantity'    => $quantity,
            'added_at'    => now()
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Item added to cart successfully',
            'data'    => ['cart_id' => $cartId]
        ], 201);
    }

    // PUT /cart/{id}
    public function update(Request $request, $id)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $userId = $user->user_id ?? $user->id;

        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cartItem = Cart::where('cart_id', $id)
                        ->where('user_id', $userId)
                        ->first();

        if (!$cartItem) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Cart item not found.'
            ], 404);
        }

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Cart quantity updated successfully.',
            'data'    => $cartItem
        ], 200);
    }

    // DELETE /cart/{id}
    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $userId = $user->user_id ?? $user->id;

        $cartItem = Cart::where('cart_id', $id)
                        ->where('user_id', $userId)
                        ->first();

        if (!$cartItem) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Cart item not found.'
            ], 404);
        }

        $cartItem->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Item removed from cart.'
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function checkout(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $userId = $user->user_id ?? $user->id;

        DB::transaction(function () use ($userId) {
            $cartItems = DB::table('carts')->where('user_id', $userId)->get();
            $totalPrice = 0;

            $saleId = DB::table('sales')->insertGetId([
                'user_id'     => $userId,
                'sale_date'   => now(),
                'total_price' => 0
            ]);

            foreach ($cartItems as $item) {
                $medicine = DB::table('medicines')->where('medicine_id', $item->medicine_id)->first();
                $subtotal = $medicine->price * $item->quantity;
                $totalPrice += $subtotal;

                DB::table('stocks')->insert([
                    'medicine_id' => $item->medicine_id,
                    'txn_type'    => 'out',
                    'quantity'    => $item->quantity,
                    'txn_date'    => now()->toDateString(),
                    'notes'       => 'OTC Sale'
                ]);

                DB::table('sale_items')->insert([
                    'sale_id'     => $saleId,
                    'medicine_id' => $item->medicine_id,
                    'quantity'    => $item->quantity,
                    'unit_price'  => $medicine->price,
                    'subtotal'    => $subtotal
                ]);
            }

            DB::table('sales')->where('sale_id', $saleId)->update(['total_price' => $totalPrice]);
            DB::table('carts')->where('user_id', $userId)->delete();
        });

        return response()->json(['message' => 'Checkout complete, pending payment']);
    }

    /**
     * GET /shop/sales
     * List the authenticated user's sales history.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $userId = $user->user_id ?? $user->id;

        $sales = DB::table('sales')
            ->where('user_id', $userId)
            ->orderByDesc('sale_date')
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => $sales
        ]);
    }

    /**
     * GET /shop/sales/{id}
     * Show a single purchase receipt: sale header + line items.
     */
    public function show(Request $request, $id)
{
    $user = $request->user();

    if (!$user) {
        return response()->json(['message' => 'Unauthenticated'], 401);
    }

    $userId = $user->user_id ?? $user->id;

    $sale = DB::table('sales')
        ->where('sale_id', $id)
        ->where('user_id', $userId)
        ->first();

    if (!$sale) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Sale not found.'
        ], 404);
    }

    $items = DB::table('sale_items')
        ->join('medicines', 'sale_items.medicine_id', '=', 'medicines.medicine_id')
        ->where('sale_items.sale_id', $id)
        ->select(
            'medicines.medicine_id',
            'medicines.medicine_name',
            'sale_items.quantity',
            'sale_items.unit_price',
            'sale_items.subtotal'
        )
        ->get();

    return response()->json([
        'status' => 'success',
        'data'   => [
            'sale'  => $sale,
            'items' => $items
        ]
    ]);
}
}

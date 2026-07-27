<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function process(Request $request, $saleId)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,credit_card,debit_card,transfer',
        ]);

        $sale = DB::table('sales')->where('sale_id', $saleId)->first();

        if (!$sale) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Sale not found.'
            ], 404);
        }

        $existingPayment = DB::table('payments')->where('sale_id', $saleId)->first();

        if ($existingPayment) {
            return response()->json([
                'status'  => 'error',
                'message' => 'This sale has already been paid.'
            ], 400);
        }

        $paymentId = DB::table('payments')->insertGetId([
            'sale_id'        => $saleId,
            'total_amount'   => $sale->total_price,
            'status'         => 'paid',
            'payment_date'   => now()->toDateString(),
            'payment_method' => $request->payment_method
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Payment recorded successfully',
            'data'    => ['payment_id' => $paymentId]
        ], 201);
    }
}

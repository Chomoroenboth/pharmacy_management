<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    // POST /shop/sales/{sale}/payment
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

    // GET /payments?search=&status=&start_date=&end_date=&page=&per_page=
    public function index(Request $request)
    {
        $authUser = $request->user();

        if (!$authUser || !($authUser instanceof Staff)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Forbidden. Staff access only.'
            ], 403);
        }

        $query = DB::table('payments as p')
            ->join('sales as sa', 'p.sale_id', '=', 'sa.sale_id')
            ->join('users as u', 'sa.user_id', '=', 'u.user_id')
            ->select(
                'p.payment_id',
                DB::raw("CONCAT('PAY-', LPAD(p.payment_id, 4, '0')) as display_id"),
                'p.sale_id',
                DB::raw("CONCAT('SL-', YEAR(sa.sale_date), '-', LPAD(sa.sale_id, 4, '0')) as sale_display_id"),
                DB::raw("CONCAT(u.first_name, ' ', u.last_name) as customer_name"),
                'p.total_amount',
                'p.payment_method',
                'p.status',
                'p.payment_date'
            );

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where(DB::raw("CONCAT(u.first_name, ' ', u.last_name)"), 'like', "%{$search}%")
                  ->orWhere('p.payment_id', 'like', "%{$search}%")
                  ->orWhere('sa.sale_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('p.status', strtolower($request->status));
        }

        if ($request->filled('start_date')) {
            $query->whereDate('p.payment_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('p.payment_date', '<=', $request->end_date);
        }

        $query->orderByDesc('p.payment_date');

        $perPage = $request->input('per_page', 5);
        $payments = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data'   => $payments->items(),
            'meta'   => [
                'current_page' => $payments->currentPage(),
                'last_page'    => $payments->lastPage(),
                'per_page'     => $payments->perPage(),
                'total'        => $payments->total(),
            ]
        ]);
    }
}

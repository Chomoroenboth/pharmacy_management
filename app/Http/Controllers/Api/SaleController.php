<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    // POST /shop/checkout
    public function checkout(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $userId = $user->user_id ?? $user->id;
        $saleId = null;

        DB::transaction(function () use ($userId, &$saleId) {
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

        return response()->json([
            'status'  => 'success',
            'message' => 'Checkout complete, pending payment',
            'sale_id' => $saleId,
        ]);
    }

    // GET /shop/sales?start_date=&end_date=&payment_status=&page=&per_page=
    // Customers see only their own sales. Staff see all sales with filters + pagination.
    public function index(Request $request)
    {
        $authUser = $request->user();

        if (!$authUser) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $query = DB::table('sales as sa')
            ->join('users as u', 'sa.user_id', '=', 'u.user_id')
            ->leftJoin('payments as p', 'p.sale_id', '=', 'sa.sale_id')
            ->select(
                'sa.sale_id',
                DB::raw("CONCAT('SL-', YEAR(sa.sale_date), '-', LPAD(sa.sale_id, 4, '0')) as display_id"),
                DB::raw("CONCAT(u.first_name, ' ', u.last_name) as customer_name"),
                'sa.user_id',
                'sa.sale_date',
                'sa.total_price',
                DB::raw("COALESCE(p.status, 'unpaid') as payment_status")
            );

        // Customers only see their own sales; staff see everything
        if (!($authUser instanceof Staff)) {
            $userId = $authUser->user_id ?? $authUser->id;
            $query->where('sa.user_id', $userId);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('sa.sale_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('sa.sale_date', '<=', $request->end_date);
        }

        if ($request->filled('payment_status')) {
            $status = strtolower($request->payment_status);

            if ($status === 'unpaid') {
                // Unpaid means either no payment row exists yet, or one exists marked unpaid
                $query->where(function ($q) {
                    $q->whereNull('p.status')->orWhere('p.status', 'unpaid');
                });
            } else {
                $query->where('p.status', $status);
            }
        }

        $query->orderByDesc('sa.sale_date');

        $perPage = $request->input('per_page', 5);
        $sales = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data'   => $sales->items(),
            'meta'   => [
                'current_page' => $sales->currentPage(),
                'last_page'    => $sales->lastPage(),
                'per_page'     => $sales->perPage(),
                'total'        => $sales->total(),
            ]
        ]);
    }

    // GET /shop/sales/{id}
    // Customers may only view their own sale. Staff may view any sale.
    public function show(Request $request, $id)
    {
        $authUser = $request->user();

        if (!$authUser) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $sale = DB::table('sales as sa')
            ->join('users as u', 'sa.user_id', '=', 'u.user_id')
            ->leftJoin('payments as p', 'p.sale_id', '=', 'sa.sale_id')
            ->where('sa.sale_id', $id)
            ->select(
                'sa.sale_id',
                DB::raw("CONCAT('SL-', YEAR(sa.sale_date), '-', LPAD(sa.sale_id, 4, '0')) as display_id"),
                'sa.user_id',
                'sa.sale_date',
                'sa.total_price',
                DB::raw("CONCAT(u.first_name, ' ', u.last_name) as customer_name"),
                DB::raw("COALESCE(p.status, 'unpaid') as payment_status"),
                'p.payment_method',
                'p.payment_date'
            )
            ->first();

        if (!$sale) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Sale not found.'
            ], 404);
        }

        // Customers may only view their own sale
        if (!($authUser instanceof Staff)) {
            $userId = $authUser->user_id ?? $authUser->id;
            if ($sale->user_id != $userId) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Sale not found.'
                ], 404);
            }
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

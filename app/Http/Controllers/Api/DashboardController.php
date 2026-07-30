<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // GET /dashboard  (staff only)
    public function index(Request $request)
    {
        $authUser = $request->user();

        if (!$authUser || !($authUser instanceof Staff)) {
            return response()->json(['status' => 'error', 'message' => 'Forbidden. Staff access only.'], 403);
        }

        $totalCustomers = DB::table('users')->count();

        $totalSalesToday = DB::table('sales')
            ->whereDate('sale_date', now()->toDateString())
            ->sum('total_price');

            $lowStock = DB::select("
        SELECT m.medicine_id, m.medicine_name, m.category,
        COALESCE(SUM(CASE WHEN s.txn_type='in' THEN s.quantity WHEN s.txn_type='out' THEN -s.quantity ELSE s.quantity END), 0) AS current_stock,
        COALESCE(MAX(s.reorder_level), 10) AS reorder_level
        FROM medicines m LEFT JOIN stocks s ON s.medicine_id = m.medicine_id
        GROUP BY m.medicine_id, m.medicine_name, m.category
        HAVING current_stock <= reorder_level
        ORDER BY current_stock ASC
        LIMIT 5
    ");

        $lowStockCount = DB::select("
            SELECT COUNT(*) as cnt FROM (
                SELECT m.medicine_id,
                  COALESCE(SUM(CASE WHEN s.txn_type='in' THEN s.quantity WHEN s.txn_type='out' THEN -s.quantity ELSE s.quantity END), 0) AS current_stock,
                  COALESCE(MAX(s.reorder_level), 10) AS reorder_level
                FROM medicines m LEFT JOIN stocks s ON s.medicine_id = m.medicine_id
                GROUP BY m.medicine_id
                HAVING current_stock <= reorder_level
            ) as low_stock_items
        ")[0]->cnt;

        $recentSales = DB::table('sales as sa')
            ->join('users as u', 'sa.user_id', '=', 'u.user_id')
            ->leftJoin('payments as p', 'p.sale_id', '=', 'sa.sale_id')
            ->select(
                'sa.sale_id',
                DB::raw("CONCAT(u.first_name, ' ', u.last_name) as customer_name"),
                'sa.total_price',
                DB::raw("CASE WHEN p.status = 'paid' THEN 'Completed' WHEN p.status = 'unpaid' THEN 'Pending' ELSE 'Pending' END as status")
            )
            ->orderByDesc('sa.sale_date')
            ->limit(5)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_customers'   => $totalCustomers,
                'total_sales_today' => $totalSalesToday,
                'low_stock_count'   => $lowStockCount,
                'recent_sales'      => $recentSales,
                'low_stock_alerts'  => $lowStock,
            ]
        ]);
    }
}

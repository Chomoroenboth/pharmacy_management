<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    // POST /inventory/stock
    public function store(Request $request)
    {
        $request->validate([
            'medicine_id'   => 'required|integer|exists:medicines,medicine_id',
            'txn_type'      => 'required|in:in,out,adjustment',
            'quantity'      => 'required|integer|min:1',
            'reorder_level' => 'nullable|integer|min:0',
            'unit_cost'     => 'nullable|numeric|min:0',
            'notes'         => 'nullable|string',
        ]);

        $stockId = DB::table('stocks')->insertGetId([
            'medicine_id'   => $request->medicine_id,
            'txn_type'      => $request->txn_type,
            'quantity'      => $request->quantity,
            'reorder_level' => $request->reorder_level ?? 10,
            'unit_cost'     => $request->unit_cost,
            'txn_date'      => now()->toDateString(),
            'notes'         => $request->notes
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Stock transaction recorded',
            'data'    => ['stock_id' => $stockId]
        ], 201);
    }
}

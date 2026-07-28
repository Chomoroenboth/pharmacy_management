<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PriceHistoryController extends Controller
{
    // GET /inventory/medicines/{medicine}/price-history
    public function index($medicineId)
    {
        $medicine = DB::table('medicines')->where('medicine_id', $medicineId)->first();

        if (!$medicine) {
            return response()->json(['status' => 'error', 'message' => 'Medicine not found'], 404);
        }

        $history = DB::table('price_historys')
            ->where('medicine_id', $medicineId)
            ->orderBy('effective_date', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => $history
        ]);
    }
}

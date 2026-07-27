<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MedicineController extends Controller
{
    // GET /inventory/medicines
    public function index(Request $request)
    {
        $medicines = DB::table('medicines as m')
            ->leftJoin('stocks as s', 'm.medicine_id', '=', 's.medicine_id')
            ->selectRaw("
                m.medicine_id,
                m.medicine_name,
                m.category,
                m.brand,
                m.price,
                m.requires_prescription,
                COALESCE(SUM(CASE
                    WHEN s.txn_type = 'in' THEN s.quantity
                    WHEN s.txn_type = 'out' THEN -s.quantity
                    WHEN s.txn_type = 'adjustment' THEN s.quantity
                    ELSE 0
                END), 0) AS current_stock,
                MAX(s.reorder_level) AS reorder_level
            ")
            ->groupBy(
                'm.medicine_id',
                'm.medicine_name',
                'm.category',
                'm.brand',
                'm.price',
                'm.requires_prescription'
            );

        if ($request->filled('category')) {
            $medicines->where('m.category', $request->query('category'));
        }

        if ($request->filled('search')) {
            $medicines->where('m.medicine_name', 'LIKE', '%' . $request->query('search') . '%');
        }

        return response()->json([
            'status' => 'success',
            'data'   => $medicines->get()
        ], 200);
    }

    // POST /inventory/medicines
    public function store(Request $request)
    {
        $request->validate([
            'medicine_name'         => 'required|string|max:100',
            'category'              => 'nullable|string|max:50',
            'brand'                 => 'nullable|string|max:50',
            'price'                 => 'required|numeric|min:0',
            'requires_prescription' => 'boolean',
        ]);

        $id = DB::table('medicines')->insertGetId([
            'medicine_name'         => $request->medicine_name,
            'category'              => $request->category,
            'brand'                 => $request->brand,
            'price'                 => $request->price,
            'requires_prescription' => $request->boolean('requires_prescription'),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Medicine added',
            'data'    => ['medicine_id' => $id]
        ], 201);
    }

    // GET /inventory/medicines/low-stock
    public function lowStockAlert()
    {
        $medicines = DB::select("
            SELECT m.medicine_id, m.medicine_name, m.category,
              COALESCE(SUM(CASE WHEN s.txn_type='in' THEN s.quantity WHEN s.txn_type='out' THEN -s.quantity ELSE s.quantity END), 0) AS current_stock,
              COALESCE(MAX(s.reorder_level), 10) AS reorder_level
            FROM medicines m LEFT JOIN stocks s ON s.medicine_id = m.medicine_id
            GROUP BY m.medicine_id, m.medicine_name, m.category
            HAVING current_stock <= reorder_level
            ORDER BY current_stock ASC
        ");

        return response()->json([
            'status' => 'success',
            'data'   => $medicines
        ]);
    }

    // GET /inventory/medicines/{id}
    public function show($id)
    {
        $medicine = Medicine::find($id);

        if (!$medicine) {
            return response()->json(['status' => 'error', 'message' => 'Medicine not found'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $medicine
        ], 200);
    }

    // DELETE /inventory/medicines/{id}
    public function destroy($id)
    {
        $medicine = Medicine::find($id);

        if (!$medicine) {
            return response()->json(['status' => 'error', 'message' => 'Medicine not found'], 404);
        }

        $medicine->delete();

        return response()->json(['status' => 'success', 'message' => 'Medicine deleted successfully'], 200);
    }

    // PUT /inventory/medicines/{medicine}/price
    public function updatePrice(Request $request, $id)
    {
        $medicine = DB::table('medicines')->where('medicine_id', $id)->first();

        if (!$medicine) {
            return response()->json(['status' => 'error', 'message' => 'Medicine not found'], 404);
        }

        $request->validate(['new_price' => 'required|numeric|min:0']);

        DB::transaction(function () use ($request, $id, $medicine) {
            DB::table('price_historys')->insert([
                'medicine_id'    => $id,
                'old_price'      => $medicine->price,
                'new_price'      => $request->new_price,
                'effective_date' => now()->toDateString()
            ]);

            DB::table('medicines')->where('medicine_id', $id)->update(['price' => $request->new_price]);
        });

        return response()->json(['status' => 'success', 'message' => 'Price updated successfully']);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InventoryController extends Controller
{
    // Fake data only — matches admin_inventory_list.jpg / admin_medicine_details.jpg.
    // Deleted once this connects to the real `medicine`/`stock`/`price_history` tables.
    private $medicines = [
        1 => ['code' => 'MED-001', 'name' => 'Amoxicillin 500mg', 'category' => 'Antibiotics', 'brand' => 'PharmaCorp', 'price' => 12.50, 'requires_prescription' => true, 'stock' => 145,
            'price_history' => [['date' => 'Oct 24, 2023', 'old' => 10.00, 'new' => 12.50]],
        ],
        2 => ['code' => 'MED-042', 'name' => 'Ibuprofen 200mg', 'category' => 'Painkillers', 'brand' => 'MediGen', 'price' => 8.99, 'requires_prescription' => false, 'stock' => 15,
            'price_history' => [['date' => 'Oct 24, 2023', 'old' => 12.50, 'new' => 14.00], ['date' => 'Jun 15, 2023', 'old' => 13.00, 'new' => 12.50]],
        ],
        3 => ['code' => 'MED-089', 'name' => 'Lisinopril 10mg', 'category' => 'Cardiovascular', 'brand' => 'HeartCare', 'price' => 24.00, 'requires_prescription' => true, 'stock' => 4,
            'price_history' => [],
        ],
        4 => ['code' => 'MED-102', 'name' => 'Vitamin D3 1000IU', 'category' => 'Vitamins', 'brand' => 'NutriHealth', 'price' => 15.75, 'requires_prescription' => false, 'stock' => 89,
            'price_history' => [],
        ],
        5 => ['code' => 'MED-015', 'name' => 'Omeprazole 20mg', 'category' => 'Gastrointestinal', 'brand' => 'StomachEase', 'price' => 18.20, 'requires_prescription' => true, 'stock' => 8,
            'price_history' => [],
        ],
    ];

    public function index()
{
    return view('admin.inventory.index');
}

    public function create()
    {
        return view('admin.inventory.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'medicine_name' => 'required|string|max:100',
            'category' => 'required|string|max:50',
            'brand' => 'nullable|string|max:50',
            'price' => 'required|numeric|min:0',
        ]);

        // TODO: real INSERT INTO medicine goes here later
        return redirect()->route('admin.inventory')->with('message', 'Medicine added (not yet connected to database)');
    }

    // GET /inventory/medicines/{id}
            public function show($id)
            {
                return view('admin.inventory.show', ['medicineId' => $id]);
            }
    public function update(Request $request, $id)
    {
        $request->validate([
            'medicine_name' => 'required|string|max:100',
            'category' => 'required|string|max:50',
            'brand' => 'nullable|string|max:50',
            'price' => 'required|numeric|min:0',
        ]);

        // TODO: real UPDATE medicine goes here later
        return redirect()->route('admin.inventory.show', $id)->with('message', 'Medicine updated (not yet connected to database)');
    }

    public function destroy($id)
    {
        // TODO: real DELETE FROM medicine goes here later
        return redirect()->route('admin.inventory')->with('message', 'Medicine deleted (not yet connected to database)');
    }

    public function restock(Request $request, $id)
    {
        $request->validate([
            'txn_type' => 'required|in:in,out,adjustment',
            'txn_date' => 'required|date',
            'quantity' => 'required|integer',
            'unit_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // TODO: real INSERT INTO stock goes here later
        return redirect()->route('admin.inventory.show', $id)->with('message', 'Stock transaction logged (not yet connected to database)');
    }

    public function editPrice($id)
    {
        $m = $this->medicines[$id] ?? $this->medicines[1];

        $medicine = (object) [
            'id' => $id,
            'name' => $m['name'],
            'price' => $m['price'],
            'stock' => $m['stock'],
        ];

        return view('admin.inventory.update-price', compact('medicine'));
    }

    public function updatePrice(Request $request, $id)
    {
        $request->validate([
            'new_price' => 'required|numeric|min:0',
            'effective_date' => 'required|date',
        ]);

        // TODO: real logic goes here later:
        // 1. INSERT INTO price_history (old_price, new_price, effective_date)
        // 2. UPDATE medicine SET price = new_price
        return redirect()->route('admin.inventory.show', $id)->with('message', 'Price updated (not yet connected to database)');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function index(Request $request)
{
    return view('admin.sales.index');
}

    public function show(string $id)
    {
        $sale = (object) [
            'sale_id' => $id === 'SL-2023-1042' ? 'SL-2023-1042' : $id,
            'customer_name' => 'Eleanor Vance',
            'date' => '2023-10-31',
            'time' => '14:30',
            'payment_method' => 'Credit Card',
            'payment_status' => 'paid',
            'total' => 61.50,
        ];

        $items = collect([
            (object) ['medicine_name' => 'Amoxicillin 500mg', 'quantity' => 30, 'unit_price' => 0.50, 'total' => 15.00],
            (object) ['medicine_name' => 'Lisinopril 10mg', 'quantity' => 90, 'unit_price' => 0.25, 'total' => 22.50],
            (object) ['medicine_name' => 'Atorvastatin 20mg', 'quantity' => 30, 'unit_price' => 0.80, 'total' => 24.00],
        ]);

        return view('admin.sales.show', compact('sale', 'items'));
    }

    public function payments(Request $request)
{
    return view('admin.payment.index');
}
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        $sales = collect([
            (object) ['sale_id' => 'SL-2023-1042', 'customer_name' => 'Eleanor Vance', 'customer_id' => 'C-8891', 'date' => '2023-10-31', 'time' => '14:30', 'total' => 145.50, 'payment_status' => 'paid'],
            (object) ['sale_id' => 'SL-2023-1041', 'customer_name' => 'Marcus Sterling', 'customer_id' => 'C-4420', 'date' => '2023-10-31', 'time' => '11:15', 'total' => 89.99, 'payment_status' => 'pending'],
            (object) ['sale_id' => 'SL-2023-1040', 'customer_name' => 'Walk-in Customer', 'customer_id' => null, 'date' => '2023-10-30', 'time' => '16:45', 'total' => 24.00, 'payment_status' => 'paid'],
            (object) ['sale_id' => 'SL-2023-1039', 'customer_name' => 'Dr. Alan Grant', 'customer_id' => 'C-1022', 'date' => '2023-10-30', 'time' => '09:05', 'total' => 310.75, 'payment_status' => 'unpaid'],
            (object) ['sale_id' => 'SL-2023-1038', 'customer_name' => 'Sarah Harding', 'customer_id' => 'C-5512', 'date' => '2023-10-29', 'time' => '13:20', 'total' => 45.00, 'payment_status' => 'paid'],
        ]);

        $search = trim((string) $request->input('search', ''));
        $status = (string) $request->input('status', '');

        if ($search !== '') {
            $sales = $sales->filter(fn($sale) => str_contains(strtolower($sale->sale_id . ' ' . $sale->customer_name), strtolower($search)));
        }

        if (in_array($status, ['paid', 'pending', 'unpaid'], true)) {
            $sales = $sales->where('payment_status', $status);
        }

        $filters = compact('search', 'status');
        $pagination = ['from' => $sales->isNotEmpty() ? 1 : 0, 'to' => $sales->count(), 'total' => 142];

        return view('admin.sales.index', compact('sales', 'filters', 'pagination'));
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
        $payments = collect([
            (object) ['payment_id' => 'PAY-8041', 'sale_id' => 'SAL-3920', 'customer_name' => 'Eleanor Shellstrop', 'amount' => 145.50, 'method' => 'Credit Card', 'status' => 'paid', 'date' => '2023-10-24', 'time' => '09:15'],
            (object) ['payment_id' => 'PAY-8042', 'sale_id' => 'SAL-3921', 'customer_name' => 'Chidi Anagonye', 'amount' => 89.00, 'method' => 'Bank Transfer', 'status' => 'unpaid', 'date' => '2023-10-24', 'time' => '10:30'],
            (object) ['payment_id' => 'PAY-8043', 'sale_id' => 'SAL-3922', 'customer_name' => 'Tahani Al-Jamil', 'amount' => 320.75, 'method' => 'Cash', 'status' => 'paid', 'date' => '2023-10-24', 'time' => '11:45'],
            (object) ['payment_id' => 'PAY-8044', 'sale_id' => 'SAL-3923', 'customer_name' => 'Jason Mendoza', 'amount' => 12.50, 'method' => 'Insurance', 'status' => 'pending', 'date' => '2023-10-24', 'time' => '14:10'],
            (object) ['payment_id' => 'PAY-8045', 'sale_id' => 'SAL-3924', 'customer_name' => 'Michael', 'amount' => 1250.00, 'method' => 'Bank Transfer', 'status' => 'paid', 'date' => '2023-10-24', 'time' => '15:22'],
        ]);

        $search = trim((string) $request->input('search', ''));
        $status = (string) $request->input('status', '');

        if ($search !== '') {
            $payments = $payments->filter(fn($payment) => str_contains(strtolower($payment->payment_id . ' ' . $payment->sale_id . ' ' . $payment->customer_name), strtolower($search)));
        }

        if (in_array($status, ['paid', 'pending', 'unpaid'], true)) {
            $payments = $payments->where('status', $status);
        }

        $filters = compact('search', 'status');
        $pagination = ['from' => $payments->isNotEmpty() ? 1 : 0, 'to' => $payments->count(), 'total' => 24];

        return view('admin.payment.index', compact('payments', 'filters', 'pagination'));
    }
}

<?php

namespace App\Http\Controllers;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalCustomers = 4289;
        $totalSalesToday = 12450;
        $salesGrowth = 5;
        $lowStockCount = 24;

        $recentSales = collect([
            (object) ['transaction_id' => '0982', 'customer_name' => 'Sarah Jenkins', 'amount' => 145.50, 'status' => 'paid'],
            (object) ['transaction_id' => '0981', 'customer_name' => 'Michael Chang', 'amount' => 89.99, 'status' => 'paid'],
            (object) ['transaction_id' => '0980', 'customer_name' => 'Emily Rodriguez', 'amount' => 210.00, 'status' => 'unpaid'],
            (object) ['transaction_id' => '0979', 'customer_name' => 'Robert Smith', 'amount' => 45.00, 'status' => 'paid'],
        ]);

        $lowStockItems = collect([
            (object) ['medicine_name' => 'Amoxicillin 500mg', 'category' => 'Antibiotics', 'quantity' => 12],
            (object) ['medicine_name' => 'Lisinopril 10mg', 'category' => 'Cardiovascular', 'quantity' => 8],
            (object) ['medicine_name' => 'Atorvastatin 20mg', 'category' => 'Cardiovascular', 'quantity' => 45],
            (object) ['medicine_name' => 'Metformin 1000mg', 'category' => 'Diabetes', 'quantity' => 5],
        ]);

        return view('admin.dashboard', compact(
            'totalCustomers',
            'totalSalesToday',
            'salesGrowth',
            'lowStockCount',
            'recentSales',
            'lowStockItems'
        ));
    }

    // Fake data only — this whole block gets deleted once real data is wired up.
    private $customers = [
        1 => [
            'display_id' => 'CUS-8924',
            'first_name' => 'Robert',
            'last_name' => 'Chen',
            'phone' => '(555) 019-2834',
            'email' => 'robert.chen@example.com',
            'dob' => '03/12/1988',
            'allergies' => ['Penicillin'],
            'prescriptions' => [['name' => 'Atorvastatin', 'dosage' => '20mg, Daily', 'status' => 'valid'], ['name' => 'Lisinopril', 'dosage' => '10mg, Daily', 'status' => 'expired']],
            'purchases' => [['date' => 'Oct 24, 2023', 'items' => 'Atorvastatin (30), Ibuprofen (1)', 'total' => 42.50], ['date' => 'Sep 15, 2023', 'items' => 'Lisinopril (30)', 'total' => 15.00]],
        ],
        2 => [
            'display_id' => 'CUS-8925',
            'first_name' => 'Sarah',
            'last_name' => 'Jenkins',
            'phone' => '(555) 482-9910',
            'email' => 's.jenkins@example.com',
            'dob' => '07/22/1990',
            'allergies' => ['Lactose Intolerance'],
            'prescriptions' => [['name' => 'Levothyroxine', 'dosage' => '50mcg, Daily', 'status' => 'valid']],
            'purchases' => [['date' => 'Oct 31, 2023', 'items' => 'Levothyroxine (30)', 'total' => 18.00]],
        ],
        3 => [
            'display_id' => 'CUS-8926',
            'first_name' => 'Michael',
            'last_name' => "O'Connor",
            'phone' => '(555) 731-0021',
            'email' => 'michael.oconnor@example.com',
            'dob' => '11/05/1975',
            'allergies' => [],
            'prescriptions' => [['name' => 'Metformin', 'dosage' => '1000mg, Twice Daily', 'status' => 'valid']],
            'purchases' => [['date' => 'Oct 30, 2023', 'items' => 'Metformin (60)', 'total' => 24.00]],
        ],
        4 => [
            'display_id' => 'CUS-8927',
            'first_name' => 'Elena',
            'last_name' => 'Rodriguez',
            'phone' => '(555) 203-8845',
            'email' => 'elena.rodriguez@example.com',
            'dob' => '02/18/1995',
            'allergies' => ['Penicillin', 'Sulfa Drugs'],
            'prescriptions' => [['name' => 'Sertraline', 'dosage' => '50mg, Daily', 'status' => 'valid'], ['name' => 'Amoxicillin', 'dosage' => '500mg, 3x Daily', 'status' => 'expired']],
            'purchases' => [['date' => 'Oct 29, 2023', 'items' => 'Sertraline (30)', 'total' => 22.50], ['date' => 'Sep 02, 2023', 'items' => 'Amoxicillin (21)', 'total' => 12.00]],
        ],
        5 => [
            'display_id' => 'CUS-8928',
            'first_name' => 'David',
            'last_name' => 'Kim',
            'phone' => '(555) 912-3347',
            'email' => 'david.kim@example.com',
            'dob' => '09/30/1982',
            'allergies' => [],
            'prescriptions' => [],
            'purchases' => [['date' => 'Oct 28, 2023', 'items' => 'Vitamin D3 1000IU (60)', 'total' => 15.75]],
        ],
    ];

    public function customers()
    {
        $customers = collect($this->customers)->map(fn($c, $id) => (object) [
            'id' => $id,
            'display_id' => $c['display_id'],
            'full_name' => $c['first_name'] . ' ' . $c['last_name'],
            'phone' => $c['phone'],
        ])->values();

        $pagination = ['from' => 1, 'to' => 5, 'total' => 124];

        return view('admin.customers.index', compact('customers', 'pagination'));
    }

    public function customerShow($id)
    {
        $c = $this->customers[$id] ?? $this->customers[1];

        $customer = (object) [
            'id' => $id,
            'display_id' => $c['display_id'],
            'first_name' => $c['first_name'],
            'last_name' => $c['last_name'],
            'full_name' => $c['first_name'] . ' ' . $c['last_name'],
            'phone' => $c['phone'],
            'email' => $c['email'],
            'dob_edit' => $c['dob'],
        ];

        $allergies = collect($c['allergies'])->map(fn($name) => (object) ['allergy_name' => $name]);
        $prescriptions = collect($c['prescriptions'])->map(fn($p) => (object) ['medicine_name' => $p['name'], 'dosage' => $p['dosage'], 'status' => $p['status']]);
        $purchases = collect($c['purchases'])->map(fn($p) => (object) ['date' => $p['date'], 'items' => $p['items'], 'total' => $p['total'], 'status' => 'Paid']);

        return view('admin.customers.show', compact('customer', 'allergies', 'prescriptions', 'purchases'));
    }

    public function customerCreate()
    {
        // Matches the real Figma reference (uploaded image) — full page,
        // not a modal. No data to pass; it's a blank form.
        return view('admin.customers.create');
    }
}

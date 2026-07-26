<?php

namespace App\Http\Controllers;

class CustomerController extends Controller
{
    public function dashboard()
    {
        $user = (object) [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone_number' => '(555) 019-8234',
            'email' => 'eleanor@thegoodplace.com',
        ];

        $purchases = collect([
            (object) ['date' => 'Oct 24, 2023', 'items' => 'Lisinopril 10mg, Ibuprofen 400mg', 'total' => 42.50, 'type' => 'Rx + OTC', 'status' => 'Paid'],
            (object) ['date' => 'Sep 15, 2023', 'items' => 'Amoxicillin 500mg', 'total' => 12.00, 'type' => 'Rx', 'status' => 'Paid'],
            (object) ['date' => 'Aug 02, 2023', 'items' => 'Cetirizine 10mg', 'total' => 18.99, 'type' => 'OTC', 'status' => 'Paid'],
        ]);

        return view('customer.dashboard', compact('user', 'purchases'));
    }

    public function profile()
    {
        // Fake/placeholder data — matches customer_profile.jpg
        // Primary Care Physician block intentionally omitted: no matching
        // column exists on the `user` table in the schema.
        $user = (object) [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'display_id' => '8891',
            'initials' => 'JD',
            'phone_number' => '(555) 019-8234',
            'email' => 'eleanor@thegoodplace.com',
            'date_of_birth' => 'Oct 14, 1982',
            'dob_edit' => '10/14/1982', // edit-form format, per customer_edit_pf.jpg
        ];

        $allergies = collect([
            (object) ['allergy_name' => 'Penicillin', 'severity' => 'warning'],
            (object) ['allergy_name' => 'Lactose Intolerance', 'severity' => 'info'],
        ]);

        $prescriptions = collect([
            (object) [
                'medicine_name' => 'Lisinopril',
                'rx_number' => '992811-A',
                'dosage' => '10mg Tablet',
                'instructions' => 'Take 1 tablet daily',
                'prescriber' => 'Dr. Sarah Jenkins',
                'status' => 'active',
            ],
            (object) [
                'medicine_name' => 'Atorvastatin',
                'rx_number' => '442910-B',
                'dosage' => '20mg Tablet',
                'instructions' => 'Take 1 tablet at bedtime',
                'prescriber' => 'Dr. Marcus Cole',
                'status' => 'filled',
            ],
            (object) [
                'medicine_name' => 'Levothyroxine',
                'rx_number' => '112099-C',
                'dosage' => '50mcg Tablet',
                'instructions' => 'Take 1 tablet daily before breakfast',
                'prescriber' => 'Dr. Sarah Jenkins',
                'status' => 'active',
            ],
        ]);

        return view('customer.profile', compact('user', 'allergies', 'prescriptions'));
    }
}

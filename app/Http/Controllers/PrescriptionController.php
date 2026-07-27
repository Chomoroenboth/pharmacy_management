<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    // Fake data only — matches admin_prescription_list.jpg / admin_prescription_details.jpg.
    // Deleted once this connects to the real `prescription`/`prescription_detail` tables.
    private $prescriptions = [
        1 => ['code' => 'RX-8492', 'customer' => 'Eleanor Vance', 'doctor' => 'Dr. Montague', 'clinic' => 'St. Jude Medical Center', 'license' => 'LIC-2023-0456',
            'issue_date' => '2023-10-12', 'expiry_date' => '2024-04-12', 'status' => 'active',
            'notes' => 'Patient reports sensitivity to sulfur-based drugs. Monitor for skin rash.',
            'medicines' => [
                ['id' => 1, 'name' => 'Amoxicillin 500mg', 'dosage' => '1 Tablet', 'quantity' => 30, 'instructions' => 'Take one daily with food'],
                ['id' => 2, 'name' => 'Lisinopril 10mg', 'dosage' => '1 Tablet', 'quantity' => 90, 'instructions' => 'One tablet at bedtime'],
            ],
        ],
        2 => ['code' => 'RX-8491', 'customer' => 'Theodora Crain', 'doctor' => 'Dr. Markway', 'clinic' => 'Hill House Clinic', 'license' => 'LIC-2022-1187',
            'issue_date' => '2023-10-10', 'expiry_date' => '2023-11-10', 'status' => 'expired',
            'notes' => '',
            'medicines' => [
                ['id' => 3, 'name' => 'Metformin 500mg', 'dosage' => '1 Tablet', 'quantity' => 60, 'instructions' => 'Take twice daily with meals'],
            ],
        ],
        3 => ['code' => 'RX-8490', 'customer' => 'Luke Sanderson', 'doctor' => 'Dr. Dudley', 'clinic' => 'Phnom Penh General Hospital', 'license' => 'LIC-2024-0812',
            'issue_date' => '2023-10-08', 'expiry_date' => '2024-01-08', 'status' => 'filled',
            'notes' => '',
            'medicines' => [
                ['id' => 4, 'name' => 'Azithromycin 500mg', 'dosage' => '1 Tablet', 'quantity' => 6, 'instructions' => 'Take once daily for 3 days'],
            ],
        ],
        4 => ['code' => 'RX-8489', 'customer' => 'Shirley Jackson', 'doctor' => 'Dr. Montague', 'clinic' => 'St. Jude Medical Center', 'license' => 'LIC-2023-0456',
            'issue_date' => '2023-10-05', 'expiry_date' => '2024-10-05', 'status' => 'active',
            'notes' => '',
            'medicines' => [
                ['id' => 5, 'name' => 'Sertraline 50mg', 'dosage' => '1 Tablet', 'quantity' => 30, 'instructions' => 'Take once daily in the morning'],
            ],
        ],
        5 => ['code' => 'RX-8488', 'customer' => 'Arthur Crain', 'doctor' => 'Dr. Markway', 'clinic' => 'Hill House Clinic', 'license' => 'LIC-2022-1187',
            'issue_date' => '2023-09-28', 'expiry_date' => '2024-03-28', 'status' => 'active',
            'notes' => '',
            'medicines' => [
                ['id' => 6, 'name' => 'Atorvastatin 20mg', 'dosage' => '1 Tablet', 'quantity' => 30, 'instructions' => 'Take once daily at bedtime'],
            ],
        ],
    ];

    public function index()
    {
        $prescriptions = collect($this->prescriptions)->map(fn($p, $id) => (object) [
            'id' => $id,
            'code' => $p['code'],
            'customer' => $p['customer'],
            'doctor' => $p['doctor'],
            'issue_date' => $p['issue_date'],
            'expiry_date' => $p['expiry_date'],
            'status' => $p['status'],
        ])->values();

        $pagination = ['from' => 1, 'to' => 5, 'total' => 42];

        return view('admin.prescriptions.index', compact('prescriptions', 'pagination'));
    }

    public function create()
    {
        return view('admin.prescriptions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer' => 'required|string',
            'doctor' => 'required|string',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date',
        ]);

        // TODO: real INSERT INTO prescription + prescription_detail goes here later.
        // Note: "doctor" is one combined field here (name + license), but the
        // schema splits it into doctor_first_name/doctor_last_name/doctor_license
        // — will need parsing when this is wired for real.
        return redirect()->route('admin.prescriptions')->with('message', 'Prescription added (not yet connected to database)');
    }

    public function show($id)
    {
        $p = $this->prescriptions[$id] ?? $this->prescriptions[1];

        $prescription = (object) [
            'id' => $id,
            'code' => $p['code'],
            'customer' => $p['customer'],
            'doctor' => $p['doctor'],
            'clinic' => $p['clinic'],
            'license' => $p['license'],
            'status' => $p['status'],
            'expiry_date' => $p['expiry_date'],
            'notes' => $p['notes'],
        ];

        $medicines = collect($p['medicines'])->map(fn($m) => (object) $m);

        return view('admin.prescriptions.show', compact('prescription', 'medicines'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'doctor' => 'required|string',
            'clinic' => 'nullable|string',
            'status' => 'required|in:active,filled,expired',
            'expiry_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        // TODO: real UPDATE prescription goes here later
        return redirect()->route('admin.prescriptions.show', $id)->with('message', 'Prescription updated (not yet connected to database)');
    }

    public function destroy($id)
    {
        // TODO: real DELETE FROM prescription goes here later
        return redirect()->route('admin.prescriptions')->with('message', 'Prescription deleted (not yet connected to database)');
    }

    public function updateMedicine(Request $request, $id, $medicineId)
    {
        $request->validate([
            'dosage' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'instructions' => 'nullable|string',
        ]);

        // TODO: real UPDATE prescription_detail goes here later
        return redirect()->route('admin.prescriptions.show', $id)->with('message', 'Medicine updated (not yet connected to database)');
    }

    public function addMedicine(Request $request, $id)
    {
        $request->validate([
            'medicine_name' => 'required|string',
            'dosage' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'instructions' => 'nullable|string',
        ]);

        // TODO: real INSERT INTO prescription_detail goes here later
        return redirect()->route('admin.prescriptions.show', $id)->with('message', 'Medicine added (not yet connected to database)');
    }

    public function removeMedicine($id, $medicineId)
    {
        // TODO: real DELETE FROM prescription_detail goes here later
        return redirect()->route('admin.prescriptions.show', $id)->with('message', 'Medicine removed (not yet connected to database)');
    }
}
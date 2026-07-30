<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class PrescriptionController extends Controller
{
    /**
     * Small inline guard since we're not using middleware yet.
     * Returns null if OK, or a 403 response if the caller isn't staff.
     */
    private function requireStaff(Request $request)
    {
        $authUser = $request->user();

        if (!$authUser || !($authUser instanceof Staff)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Forbidden. Staff access only.'
            ], 403);
        }

        return null;
    }

    // GET /prescriptions
    // Staff: see all prescriptions. Customer: see only their own.
    // GET /prescriptions
// Staff: see all prescriptions (paginated, joined with customer name + generated display code).
// Customer: see only their own.
public function index(Request $request)
{
    $authUser = $request->user();

    if (!$authUser) {
        return response()->json(['message' => 'Unauthenticated'], 401);
    }

    $query = DB::table('prescriptions as pr')
        ->join('users as u', 'pr.user_id', '=', 'u.user_id')
        ->select(
            'pr.prescription_id',
            DB::raw("CONCAT('RX-', LPAD(pr.prescription_id, 4, '0')) as display_code"),
            DB::raw("CONCAT(u.first_name, ' ', u.last_name) as customer_name"),
            'pr.user_id',
            DB::raw("CONCAT('Dr. ', pr.doctor_first_name, ' ', COALESCE(pr.doctor_last_name, '')) as doctor_name"),
            'pr.doctor_first_name',
            'pr.doctor_last_name',
            'pr.doctor_license',
            'pr.doctor_clinic',
            'pr.issue_date',
            'pr.expiry_date',
            'pr.status',
            'pr.notes'
        );

    if (!($authUser instanceof Staff)) {
        $userId = $authUser->user_id ?? $authUser->id;
        $query->where('pr.user_id', $userId);
    }

    $query->orderByDesc('pr.issue_date');

    $perPage = $request->input('per_page', 5);
    $prescriptions = $query->paginate($perPage);

    return response()->json([
        'status' => 'success',
        'data'   => $prescriptions->items(),
        'meta'   => [
            'current_page' => $prescriptions->currentPage(),
            'last_page'    => $prescriptions->lastPage(),
            'per_page'     => $prescriptions->perPage(),
            'total'        => $prescriptions->total(),
        ]
    ]);
}

    // POST /prescriptions  (staff only, creates on behalf of a customer)
    public function store(Request $request)
    {
        if ($resp = $this->requireStaff($request)) return $resp;

        $request->validate([
            'user_id'           => 'required|integer|exists:users,user_id',
            'doctor_first_name' => 'required|string|max:30',
            'issue_date'        => 'required|date',
            'medicines'         => 'required|array|min:1',
            'medicines.*.medicine_id' => 'required|integer|exists:medicines,medicine_id',
            'medicines.*.quantity'    => 'required|integer|min:1',
        ]);

        $prescriptionId = DB::transaction(function () use ($request) {
            $prescriptionId = DB::table('prescriptions')->insertGetId([
                'user_id'           => $request->user_id,
                'doctor_first_name' => $request->doctor_first_name,
                'doctor_last_name'  => $request->doctor_last_name,
                'doctor_license'    => $request->doctor_license,
                'doctor_clinic'     => $request->doctor_clinic,
                'issue_date'        => $request->issue_date,
                'expiry_date'       => $request->expiry_date,
                'status'            => 'active',
                'notes'             => $request->notes
            ]);

            foreach ($request->medicines as $med) {
                DB::table('prescription_details')->insert([
                    'prescription_id' => $prescriptionId,
                    'medicine_id'     => $med['medicine_id'],
                    'dosage'          => $med['dosage'] ?? null,
                    'quantity'        => $med['quantity'],
                    'instructions'    => $med['instructions'] ?? null
                ]);
            }

            return $prescriptionId;
        });

        return response()->json([
            'status'  => 'success',
            'message' => 'Prescription created successfully',
            'data'    => ['prescription_id' => $prescriptionId]
        ], 201);
    }
    // GET /prescriptions/{id}
// Staff: can view any prescription. Customer: only their own.
    public function show(Request $request, $id)
    {
        $authUser = $request->user();

        if (!$authUser) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $prescription = DB::table('prescriptions as pr')
            ->join('users as u', 'pr.user_id', '=', 'u.user_id')
            ->select(
                'pr.prescription_id',
                DB::raw("CONCAT('RX-', LPAD(pr.prescription_id, 4, '0')) as display_code"),
                DB::raw("CONCAT(u.first_name, ' ', u.last_name) as customer_name"),
                'pr.user_id',
                DB::raw("CONCAT('Dr. ', pr.doctor_first_name, ' ', COALESCE(pr.doctor_last_name, '')) as doctor_name"),
                'pr.doctor_first_name',
                'pr.doctor_last_name',
                'pr.doctor_license',
                'pr.doctor_clinic',
                'pr.issue_date',
                'pr.expiry_date',
                'pr.status',
                'pr.notes'
            )
            ->where('pr.prescription_id', $id)
            ->first();

        if (!$prescription) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Prescription not found.'
            ], 404);
        }

        // Customers may only view their own prescription
        if (!($authUser instanceof Staff)) {
            $userId = $authUser->user_id ?? $authUser->id;
            if ($prescription->user_id != $userId) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Forbidden.'
                ], 403);
            }
        }

        $medicines = DB::table('prescription_details as pd')
            ->join('medicines as m', 'pd.medicine_id', '=', 'm.medicine_id')
            ->select(
                'pd.detail_id',
                'pd.medicine_id',
                'm.medicine_name',
                'pd.dosage',
                'pd.quantity',
                'pd.instructions'
            )
            ->where('pd.prescription_id', $id)
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => [
                'prescription' => $prescription,
                'medicines'    => $medicines,
            ]
        ]);
    }
    // PUT /prescriptions/{id}  (staff only, edits core prescription fields)
    public function update(Request $request, $id)
    {
        if ($resp = $this->requireStaff($request)) return $resp;

        $prescription = DB::table('prescriptions')->where('prescription_id', $id)->first();

        if (!$prescription) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Prescription not found.'
            ], 404);
        }

        $request->validate([
            'status' => 'sometimes|in:active,filled,expired',
        ]);

        DB::table('prescriptions')->where('prescription_id', $id)->update(
            $request->only(['status', 'expiry_date', 'notes'])
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Prescription updated successfully.'
        ]);
    }

    // DELETE /prescriptions/{id}  (staff only)
    public function destroy(Request $request, $id)
    {
        if ($resp = $this->requireStaff($request)) return $resp;

        $prescription = DB::table('prescriptions')->where('prescription_id', $id)->first();

        if (!$prescription) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Prescription not found.'
            ], 404);
        }

        DB::transaction(function () use ($id) {
            DB::table('prescription_details')->where('prescription_id', $id)->delete();
            DB::table('prescriptions')->where('prescription_id', $id)->delete();
        });

        return response()->json([
            'status'  => 'success',
            'message' => 'Prescription deleted successfully.'
        ]);
    }

    // POST /prescriptions/{id}/medicines  (staff only, "+ Add Row")
    public function addMedicine(Request $request, $id)
    {
        if ($resp = $this->requireStaff($request)) return $resp;

        $prescription = DB::table('prescriptions')->where('prescription_id', $id)->first();

        if (!$prescription) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Prescription not found.'
            ], 404);
        }

        $request->validate([
            'medicine_id' => 'required|integer|exists:medicines,medicine_id',
            'dosage'      => 'nullable|string|max:50',
            'quantity'    => 'required|integer|min:1',
            'instructions'=> 'nullable|string',
        ]);

        $detailId = DB::table('prescription_details')->insertGetId([
            'prescription_id' => $id,
            'medicine_id'     => $request->medicine_id,
            'dosage'          => $request->dosage,
            'quantity'        => $request->quantity,
            'instructions'    => $request->instructions
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Medicine added to prescription.',
            'data'    => ['detail_id' => $detailId]
        ], 201);
    }

    // PUT /prescriptions/medicines/{detailId}  (staff only, "Edit Medicine")
    public function updateMedicine(Request $request, $detailId)
    {
        if ($resp = $this->requireStaff($request)) return $resp;

        $detail = DB::table('prescription_details')->where('detail_id', $detailId)->first();

        if (!$detail) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Prescription medicine entry not found.'
            ], 404);
        }

        DB::table('prescription_details')->where('detail_id', $detailId)->update(
            $request->only(['dosage', 'quantity', 'instructions'])
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Medicine entry updated.'
        ]);
    }

    // DELETE /prescriptions/medicines/{detailId}  (staff only, "Remove Medicine?")
    public function removeMedicine(Request $request, $detailId)
    {
        if ($resp = $this->requireStaff($request)) return $resp;

        $detail = DB::table('prescription_details')->where('detail_id', $detailId)->first();

        if (!$detail) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Prescription medicine entry not found.'
            ], 404);
        }

        DB::table('prescription_details')->where('detail_id', $detailId)->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Medicine removed from prescription.'
        ]);
    }

    // POST /prescriptions/{id}/dispense  (staff only — "Dispense" button)
    public function dispense(Request $request, $id)
    {
        if ($resp = $this->requireStaff($request)) return $resp;

        $staff = $request->user();
        $staffId = $staff->staff_id;

        try {
            $saleId = DB::transaction(function () use ($request, $id, $staffId) {
                $prescription = DB::table('prescriptions')
                    ->where('prescription_id', $id)
                    ->where('status', 'active')
                    ->where('expiry_date', '>=', now()->toDateString())
                    ->first();

                if (!$prescription) {
                    throw new Exception('Prescription is invalid or expired.');
                }

                $details = DB::table('prescription_details')->where('prescription_id', $id)->get();

                if ($details->isEmpty()) {
                    throw new Exception('Prescription has no medicine items.');
                }

                $totalPrice = 0;

                $saleId = DB::table('sales')->insertGetId([
                    'user_id'         => $prescription->user_id,
                    'prescription_id' => $id,
                    'staff_id'        => $staffId,
                    'sale_date'       => now(),
                    'total_price'     => 0
                ]);

                foreach ($details as $detail) {
                    $medicine = DB::table('medicines')->where('medicine_id', $detail->medicine_id)->first();
                    $subtotal = $medicine->price * $detail->quantity;
                    $totalPrice += $subtotal;

                    DB::table('stocks')->insert([
                        'medicine_id' => $detail->medicine_id,
                        'txn_type'    => 'out',
                        'quantity'    => $detail->quantity,
                        'txn_date'    => now()->toDateString(),
                        'notes'       => 'Prescription Dispense'
                    ]);

                    DB::table('sale_items')->insert([
                        'sale_id'     => $saleId,
                        'medicine_id' => $detail->medicine_id,
                        'quantity'    => $detail->quantity,
                        'unit_price'  => $medicine->price,
                        'subtotal'    => $subtotal
                    ]);
                }

                DB::table('sales')->where('sale_id', $saleId)->update(['total_price' => $totalPrice]);

                DB::table('payments')->insert([
                    'sale_id'        => $saleId,
                    'total_amount'   => $totalPrice,
                    'status'         => 'paid',
                    'payment_date'   => now()->toDateString(),
                    'payment_method' => $request->payment_method
                ]);

                DB::table('prescriptions')->where('prescription_id', $id)->update(['status' => 'filled']);

                return $saleId;
            });

            return response()->json([
                'status'  => 'success',
                'message' => 'Prescription filled and sale processed',
                'data'    => ['sale_id' => $saleId]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }
}

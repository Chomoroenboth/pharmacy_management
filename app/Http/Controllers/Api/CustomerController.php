<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    // GET /staff/customers
   public function index(Request $request)
{
    $query = DB::table('users')
        ->select(
            'user_id',
            DB::raw("CONCAT('CUS-', LPAD(user_id, 4, '0')) as display_id"),
            DB::raw("CONCAT(first_name, ' ', COALESCE(last_name, '')) as full_name"),
            'first_name',
            'last_name',
            'email',
            'phone_number',
            'date_of_birth',
            'address'
        );

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }

    $query->orderBy('user_id');

    $perPage = $request->input('per_page', 5);
    $customers = $query->paginate($perPage);

    return response()->json([
        'status' => 'success',
        'data'   => $customers->items(),
        'meta'   => [
            'current_page' => $customers->currentPage(),
            'last_page'    => $customers->lastPage(),
            'per_page'     => $customers->perPage(),
            'total'        => $customers->total(),
        ]
    ]);
}
    // GET /staff/customers/{id}
    public function show($id)
{
    $customer = DB::table('users')
        ->select(
            'user_id',
            DB::raw("CONCAT('CUS-', LPAD(user_id, 4, '0')) as display_id"),
            'first_name',
            'last_name',
            DB::raw("CONCAT(first_name, ' ', COALESCE(last_name, '')) as full_name"),
            'email',
            'phone_number',
            'date_of_birth',
            'address'
        )
        ->where('user_id', $id)
        ->first();

    if (!$customer) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Customer not found.'
        ], 404);
    }

    $allergies = DB::table('allergies')->where('user_id', $id)->get();

    $prescriptions = DB::table('prescriptions as pr')
        ->join('prescription_details as pd', 'pd.prescription_id', '=', 'pr.prescription_id')
        ->join('medicines as m', 'pd.medicine_id', '=', 'm.medicine_id')
        ->where('pr.user_id', $id)
        ->select('m.medicine_name', 'pd.dosage', 'pr.status')
        ->orderByDesc('pr.issue_date')
        ->get();

    $purchases = DB::table('sales as sa')
        ->leftJoin('payments as p', 'p.sale_id', '=', 'sa.sale_id')
        ->where('sa.user_id', $id)
        ->select(
            'sa.sale_id',
            'sa.sale_date',
            'sa.total_price',
            DB::raw("COALESCE(p.status, 'unpaid') as payment_status")
        )
        ->orderByDesc('sa.sale_date')
        ->get();

    foreach ($purchases as $purchase) {
        $items = DB::table('sale_items as si')
            ->join('medicines as m', 'si.medicine_id', '=', 'm.medicine_id')
            ->where('si.sale_id', $purchase->sale_id)
            ->select(DB::raw("CONCAT(m.medicine_name, ' (', si.quantity, ')') as item_str"))
            ->pluck('item_str');
        $purchase->items = $items->implode(', ');
    }

    return response()->json([
        'status' => 'success',
        'data'   => [
            'customer'      => $customer,
            'allergies'     => $allergies,
            'prescriptions' => $prescriptions,
            'purchases'     => $purchases
        ]
    ]);
}
    // POST /staff/customers
    public function store(Request $request)
    {
        $request->validate([
            'first_name'    => 'required|string|max:30',
            'last_name'     => 'nullable|string|max:30',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|string|min:6',
            'phone_number'  => 'nullable|string|unique:users,phone_number',
            'date_of_birth' => 'nullable|date',
            'address'       => 'nullable|string|max:50',
        ]);

        $customer = User::create([
            'first_name'    => $request->first_name,
            'last_name'     => $request->last_name,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'phone_number'  => $request->phone_number,
            'date_of_birth' => $request->date_of_birth,
            'address'       => $request->address,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Customer created successfully.',
            'data'    => $customer
        ], 201);
    }

    // PUT /staff/customers/{id}
    public function update(Request $request, $id)
    {
        $customer = User::find($id);

        if (!$customer) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Customer not found.'
            ], 404);
        }

        $customer->update(
    $request->only(['first_name', 'last_name', 'email', 'phone_number', 'address', 'date_of_birth'])
);

        return response()->json([
            'status'  => 'success',
            'message' => 'Customer updated successfully.',
            'data'    => $customer
        ]);
    }

    // DELETE /staff/customers/{id}
    public function destroy($id)
    {
        $customer = User::find($id);

        if (!$customer) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Customer not found.'
            ], 404);
        }

        $customer->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Customer deleted successfully.'
        ]);
    }
}

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
    public function index()
    {
        $customers = User::select('user_id', 'first_name', 'last_name', 'email', 'phone_number', 'date_of_birth', 'address')->get();

        return response()->json([
            'status' => 'success',
            'data'   => $customers
        ]);
    }

    // GET /staff/customers/{id}
    public function show($id)
    {
        $customer = User::find($id);

        if (!$customer) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Customer not found.'
            ], 404);
        }

        $allergies = DB::table('allergies')->where('user_id', $id)->get();

        return response()->json([
            'status' => 'success',
            'data'   => [
                'customer'  => $customer,
                'allergies' => $allergies
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
            $request->only(['first_name', 'last_name', 'phone_number', 'address', 'date_of_birth'])
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

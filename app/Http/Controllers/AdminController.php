<?php

namespace App\Http\Controllers;

class AdminController extends Controller
{
    public function dashboard()
{
    return view('admin.dashboard');
}
    public function customers()
{
    return view('admin.customers.index');
}

public function customerShow($id)
{
    return view('admin.customers.show', ['customerId' => $id]);
}

public function customerCreate()
{
    return view('admin.customers.create');
}
}

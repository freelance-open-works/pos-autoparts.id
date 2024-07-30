<?php

namespace App\Http\Controllers\Default;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Default\Role;
use App\Models\Default\User;
use App\Models\Product;
use App\Models\Supplier;

class GeneralController extends Controller
{
    public function index()
    {
        return inertia('Dashboard', [
            'role_count' => Role::count(),
            'user_count' => User::count(),
            'customer_count' => Customer::count(),
            'supplier_count' => Supplier::count(),
            'product_count' => Product::count(),
        ]);
    }

    public function maintance()
    {
        return inertia('Maintance');
    }
}

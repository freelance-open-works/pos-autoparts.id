<?php

namespace App\Http\Controllers\Default;

use App\Http\Controllers\Controller;
use App\Models\Default\Role;
use App\Models\Default\User;

class GeneralController extends Controller
{
    public function index()
    {
        return inertia('Dashboard', [
            'role_count' => Role::count(),
            'user_count' => User::count(),
        ]);
    }

    public function maintance()
    {
        return inertia('Maintance');
    }
}

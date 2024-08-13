<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StoreOrder;
use Illuminate\Http\Request;

class StoreOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = StoreOrder::with(['supplier', 'items.product.brand']);

        if ($request->q) {
            $query->where(function ($query) use ($request) {
                $query->where('so_code', 'like', "%{$request->q}%")
                    ->orWhere('status', 'like', "%{$request->q}%");
            });
        }

        $query->orderBy('created_at', 'desc');

        return $query->paginate();
    }
}

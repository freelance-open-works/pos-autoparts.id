<?php

namespace App\Http\Controllers\Default;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Customer;
use App\Models\Default\Role;
use App\Models\Default\User;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class GeneralController extends Controller
{
    public function index(Request $request)
    {
        $total_sale_month = Sale::whereMonth('s_date', now()->format('m'))->sum('amount_cost');
        $total_sale_today = Sale::where(DB::raw('DATE(s_date)'), now()->format('Y-m-d'))->sum('amount_cost');
        $items_sale_today = SaleItem::whereIn(
            'sale_id',
            Sale::where(DB::raw('DATE(s_date)'), now()->format('Y-m-d'))->pluck('id')->toArray()
        )->sum('qty');

        $top_products = SaleItem::join('products', 'products.id', '=', 'sale_items.product_id')
            ->select('sale_items.product_id', 'products.name',  'products.part_code', DB::raw('SUM(sale_items.qty) as most_qty'))
            ->groupBy('sale_items.product_id')
            ->orderBy('most_qty', 'DESC')
            ->limit(10)
            ->get();

        return inertia('Dashboard', [
            'role_count' => Role::count(),
            'user_count' => User::count(),
            'customer_count' => Customer::count(),
            'supplier_count' => Supplier::count(),
            'product_count' => Product::count(),
            'month' => now()->translatedFormat('F'),
            'total_sale_month' => $total_sale_month,
            'total_sale_today' => $total_sale_today,
            'items_sale_today' => $items_sale_today,
            'top_products' => $top_products,
            'charts' => $this->charts($request),
            'brands' => Brand::all(),
            'users' => User::all(),
            'types' => [Customer::INCITY, Customer::OUTCITY]
        ]);
    }

    private function charts(Request $request)
    {
        $startDate = now()->startOfWeek();
        $endDate = now()->endOfWeek();

        if ($request->startDate != '' && $request->endDate != '') {
            $startDate = Carbon::parse($request->startDate);
            $endDate = Carbon::parse($request->endDate);
        }

        $charts = [
            'sales' => [],
            'purchases' => [],
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
        ];

        // filter penjualan merk tertentu
        // filter users
        // filter customer

        $sales = DB::table('sale_items')
            ->leftJoin('sales',  'sales.id', '=', 'sale_items.sale_id')
            ->leftJoin('products', 'products.id', '=', 'sale_items.product_id')
            ->leftJoin('customers', 'customers.id', '=', 'sales.customer_id')
            ->selectRaw('sum(sale_items.price * sale_items.qty) as pq_total, sum(sale_items.qty) as pq_qty, DATE(sales.s_date) as s_s_date')
            ->groupBy('s_s_date');

        $purchases = DB::table('purchase_items')
            ->leftJoin('purchases',  'purchases.id', '=', 'purchase_items.purchase_id')
            ->leftJoin('products', 'products.id', '=', 'purchase_items.product_id')
            ->leftJoin('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')
            ->selectRaw('sum(purchase_items.cost * purchase_items.qty) as pq_total, sum(purchase_items.qty) as pq_qty, DATE(purchases.p_date) as ps_date')
            ->groupBy('ps_date');

        if ($request->brand != '') {
            $brand = Brand::where('name', $request->brand)->first();
            $purchases->where('products.brand_id', $brand->id);
            $sales->where('products.brand_id', $brand->id);
        }

        if ($request->user != '') {
            $user = User::where('name', $request->user)->first();
            $purchases->where('purchases.created_by', $user->id);
            $sales->where('sales.created_by', $user->id);
        }

        if ($request->type != '') {
            $purchases->where('suppliers.type', $request->type);
            $sales->where('customers.type', $request->type);
        }

        $purchases = $purchases->get()->mapWithKeys(function ($item) {
            return [$item->ps_date => [
                'total' => $item->pq_total,
                'qty' => $item->pq_qty,
            ]];
        });

        $sales = $sales->get()->mapWithKeys(function ($item) {
            info(self::class, [$item]);
            return [$item->s_s_date => [
                'total' => $item->pq_total,
                'qty' => $item?->pq_qty,
            ]];
        });

        $std = Carbon::parse($startDate);
        while ($std <= $endDate) {
            $charts['sales'][] = [
                'date' => $std->format('d-m-Y'),
                'data' =>  $sales[$std->format('Y-m-d')] ?? ['total' => 0, 'qty' => 0],
            ];
            $charts['purchases'][] = [
                'date' => $std->format('d-m-Y'),
                'data' => $purchases[$std->format('Y-m-d')] ?? ['total' => 0, 'qty' => 0],
            ];
            $std = $std->addDay();
        }

        return $charts;
    }

    public function maintance()
    {
        return inertia('Maintance');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Rap2hpoutre\FastExcel\FastExcel;

class ProductImportController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'products' => 'string|required'
        ]);

        if (Storage::disk('local')->exists('public/' . $request->products)) {
            $brands = Brand::all()->mapWithKeys(function ($b) {
                return [$b->name => $b->id];
            });

            $path = Storage::disk('local')->path('public/' . $request->products);
            $sheet = (new FastExcel())->import($path);

            foreach ($sheet as $product) {
                Product::updateOrCreate([
                    'part_code' => $product['Part No'],
                ], [
                    'name' => $product['Nama Barang'],
                    'type' => $product['Type Barang'],
                    'discount' => $product['Discount'],
                    'cost' => $product['Harga Beli'],
                    'price' => $product['Harga Jual'],
                    'brand_id' => $brands[$product['Merk']] ?? $brands[0],
                ]);
            }
        }

        return redirect()->route('products.index')
            ->with('message', ['type' => 'success', 'message' => 'Items has beed updated']);
    }
}

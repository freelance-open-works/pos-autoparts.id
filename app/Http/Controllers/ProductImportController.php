<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Rap2hpoutre\FastExcel\FastExcel;

class ProductImportController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'products' => 'string|required'
        ], [
            'products.string' => 'pastikan file terupload 100%'
        ]);

        if (Storage::disk('local')->exists('public/' . $request->products)) {
            $brands = Brand::all()->mapWithKeys(function ($b) {
                return [$b->name => $b->id];
            });

            try {
                DB::beginTransaction();
                $path = Storage::disk('local')->path('public/' . $request->products);
                $sheet = (new FastExcel())->import($path);

                foreach ($sheet as $product) {
                    $p = Product::updateOrCreate([
                        'part_code' => Str::trim($product['Part No']),
                    ], [
                        'name' => Str::trim($product['Nama Barang']),
                        'type' => Str::trim($product['Type Barang']),
                        'discount' => Str::trim($product['Discount']),
                        'cost' => Str::trim($product['Harga Beli']),
                        'price' => Str::trim($product['Harga Jual']),
                        'brand_id' => $brands[Str::trim($product['Merk'])] ?? $brands[0],
                    ]);
                    info(self::class, [$p, $product['Part No'], $product['Harga Beli']]);
                }
                DB::commit();
            } catch (Exception $e) {
                DB::rollback();
                info(self::class, [$e->getMessage()]);
                return redirect()->route('products.index')
                    ->with('message', ['type' => 'error', 'message' => 'tidak dapat memproses import pada file, terjadi kesalahan']);
            }
        }

        return redirect()->route('products.index')
            ->with('message', ['type' => 'success', 'message' => 'Items has beed updated']);
    }
}

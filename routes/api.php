<?php

use App\Http\Controllers\Api\ProductContoller;
use App\Http\Controllers\Api\PurchaseController;
use App\Http\Controllers\Api\PurchaseOrderController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\StoreOrderController;
use App\Http\Controllers\Default\Api\SelectTableController;
use App\Http\Controllers\Default\FileController;
use App\Http\Middleware\JwtCustomApiVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware([JwtCustomApiVerification::class])
    ->prefix('_default')
    ->group(function () {
        Route::get('/select/{table}', SelectTableController::class)->name('api.select.table');
        Route::post('files', [FileController::class, 'store'])->name('api.file.store');

        Route::get('/products', [ProductContoller::class, 'index'])->name('api.products.index');
        Route::get('/purchases', [PurchaseController::class, 'index'])->name('api.purchases.index');
        Route::get('/sales', [SaleController::class, 'index'])->name('api.sales.index');
        Route::get('/purchase-orders', [PurchaseOrderController::class, 'index'])->name('api.purchase-orders.index');
        Route::get('/store-orders', [StoreOrderController::class, 'index'])->name('api.store-orders.index');
    });

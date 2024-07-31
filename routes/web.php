<?php

use App\Http\Controllers\ClaimController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\ProductStockController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ExpeditionController;
use App\Http\Controllers\Default\FileController;
use App\Http\Controllers\Default\GeneralController;
use App\Http\Controllers\Default\PermissionController;
use App\Http\Controllers\Default\ProfileController;
use App\Http\Controllers\Default\RoleController;
use App\Http\Controllers\Default\SettingController;
use App\Http\Controllers\Default\UserController;
use App\Http\Controllers\ReportPurchaseController;
use App\Http\Controllers\ReportSaleController;
use App\Http\Controllers\SaleDeliveryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('files/{file}', [FileController::class, 'show'])->name('file.show');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [GeneralController::class, 'index'])->name('dashboard');
    Route::get('/maintance', [GeneralController::class, 'maintance'])->name('maintance');

    // User
    Route::get('/users', [UserController::class, 'index'])->name('user.index');
    Route::post('/users', [UserController::class, 'store'])->name('user.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('user.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('user.destroy');

    // Permission
    Route::delete('_permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');
    Route::put('_permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update');
    Route::post('_permissions', [PermissionController::class, 'store'])->name('permissions.store');
    Route::get('_permissions', [PermissionController::class, 'index'])->name('permissions.index');

    // Role
    Route::resource('/roles', RoleController::class);

    // Setting
    Route::get('/settings', [SettingController::class, 'index'])->name('setting.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('setting.update');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // #Admin
    Route::get('/report/purchases/export', [ReportPurchaseController::class, 'export'])->name('report.purchases.export');
    Route::get('/report/purchases', [ReportPurchaseController::class, 'index'])->name('report.purchases');

    Route::get('/report/sales/export', [ReportSaleController::class, 'export'])->name('report.sales.export');
    Route::get('/report/sales', [ReportSaleController::class, 'index'])->name('report.sales');

    Route::patch('claims/{claim}/patch', [ClaimController::class, 'patch'])->name('claims.patch');
    Route::resource('claims', ClaimController::class);

    Route::get('sales/{sale}/delivery-print', [SaleDeliveryController::class, 'print'])->name('sales.delivery-print');
    Route::post('sales/{sale}/delivery', [SaleDeliveryController::class, 'update'])->name('sales.delivery');
    Route::get('sales/{sale}/print', [SaleController::class, 'print_invoice'])->name('sales.print');
    Route::patch('sales/{sale}/patch', [SaleController::class, 'patch'])->name('sales.patch');
    Route::resource('sales', SaleController::class);

    Route::get('purchases/{purchase}/print', [PurchaseController::class, 'print'])->name('purchases.print');
    Route::patch('purchases/{purchase}/patch', [PurchaseController::class, 'patch'])->name('purchases.patch');
    Route::resource('purchases', PurchaseController::class);

    Route::get('purchase-orders/{purchaseOrder}/print', [PurchaseOrderController::class, 'print'])->name('purchase-orders.print');
    Route::patch('purchase-orders/{purchaseOrder}/patch', [PurchaseOrderController::class, 'patch'])->name('purchase-orders.patch');
    Route::resource('purchase-orders', PurchaseOrderController::class);

    Route::get('product-stocks', [ProductStockController::class, 'index'])->name('product-stocks.index');

    Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::post('products', [ProductController::class, 'store'])->name('products.store');
    Route::get('products', [ProductController::class, 'index'])->name('products.index');

    Route::delete('suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
    Route::put('suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
    Route::post('suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
    Route::get('suppliers', [SupplierController::class, 'index'])->name('suppliers.index');

    Route::delete('customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
    Route::put('customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::post('customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');

    Route::delete('expeditions/{expedition}', [ExpeditionController::class, 'destroy'])->name('expeditions.destroy');
    Route::put('expeditions/{expedition}', [ExpeditionController::class, 'update'])->name('expeditions.update');
    Route::post('expeditions', [ExpeditionController::class, 'store'])->name('expeditions.store');
    Route::get('expeditions', [ExpeditionController::class, 'index'])->name('expeditions.index');
});

// #Guest
<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\WorkOrderController;
use App\Http\Controllers\OrderBatchController;
use App\Http\Controllers\AttributeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\CategoryAttributeController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

// ── Guest Routes ──
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::post('/login', [LoginController::class, 'login']);
});

// ── Authenticated Routes ──
Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // ── Settings ──
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

    // ── Users Management ──
    Route::middleware('permission:users.view')->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
    });
    Route::middleware('permission:users.create')->group(function () {
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
    });
    Route::middleware('permission:users.edit')->group(function () {
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
    });
    Route::middleware('permission:users.delete')->group(function () {
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // ── Employees Management ──
    Route::resource('employees', EmployeeController::class);

    // ── CRUD Resources ──
    // Categories
    Route::middleware('permission:categories.create')->group(function () {
        Route::get('categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('categories/template', [CategoryController::class, 'exportTemplate'])->name('categories.template');
        Route::post('categories/import', [CategoryController::class, 'import'])->name('categories.import');
    });
    Route::middleware('permission:categories.view')->group(function () {
        Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
        Route::get('categories/{category}/attributes', [CategoryController::class, 'getAttributes'])->name('categories.attributes');
    });
    Route::middleware('permission:categories.edit')->group(function () {
        Route::get('categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    });
    Route::middleware('permission:categories.delete')->group(function () {
        Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    });

    // Products
    Route::middleware('permission:products.create')->group(function () {
        Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('products', [ProductController::class, 'store'])->name('products.store');
        Route::get('products/template', [ProductController::class, 'exportTemplate'])->name('products.template');
        Route::post('products/import', [ProductController::class, 'import'])->name('products.import');
    });
    Route::middleware('permission:products.view')->group(function () {
        Route::get('products', [ProductController::class, 'index'])->name('products.index');
        Route::get('products/{product}', [ProductController::class, 'show'])->name('products.show');
    });
    Route::middleware('permission:products.edit')->group(function () {
        Route::get('products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update');
    });
    Route::middleware('permission:products.delete')->group(function () {
        Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    });

    // Clients
    Route::middleware('permission:clients.create')->group(function () {
        Route::get('clients/create', [ClientController::class, 'create'])->name('clients.create');
        Route::post('clients', [ClientController::class, 'store'])->name('clients.store');
        Route::get('clients/template', [ClientController::class, 'exportTemplate'])->name('clients.template');
        Route::post('clients/import', [ClientController::class, 'import'])->name('clients.import');
    });
    Route::middleware('permission:clients.view')->group(function () {
        Route::get('clients', [ClientController::class, 'index'])->name('clients.index');
        Route::get('clients/{client}', [ClientController::class, 'show'])->name('clients.show');
    });
    Route::middleware('permission:clients.edit')->group(function () {
        Route::post('clients/sort-batch', [ClientController::class, 'updateSortBatch'])->name('clients.updateSortBatch');
        Route::get('clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
        Route::put('clients/{client}', [ClientController::class, 'update'])->name('clients.update');
        Route::patch('clients/{client}/sort', [ClientController::class, 'updateSort'])->name('clients.updateSort');
    });
    Route::middleware('permission:clients.delete')->group(function () {
        Route::delete('clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');
        Route::delete('clients', [ClientController::class, 'bulkDestroy'])->name('clients.bulkDestroy');
    });

    // Work Orders
    Route::middleware('permission:work-orders.create')->group(function () {
        Route::get('work-orders/create', [WorkOrderController::class, 'create'])->name('work-orders.create');
        Route::post('work-orders', [WorkOrderController::class, 'store'])->name('work-orders.store');
    });
    Route::middleware('permission:work-orders.view')->group(function () {
        Route::get('work-orders', [WorkOrderController::class, 'index'])->name('work-orders.index');
        Route::get('work-orders/{workOrder}', [WorkOrderController::class, 'show'])->name('work-orders.show');
        Route::get('work-orders/{workOrder}/duplicate', [WorkOrderController::class, 'duplicate'])->name('work-orders.duplicate');
        Route::get('ajax/products/search', [WorkOrderController::class, 'searchProducts'])->name('ajax.products.search');
    });
    Route::middleware('permission:work-orders.edit')->group(function () {
        Route::get('work-orders/{workOrder}/edit', [WorkOrderController::class, 'edit'])->name('work-orders.edit');
        Route::put('work-orders/{workOrder}', [WorkOrderController::class, 'update'])->name('work-orders.update');
        Route::patch('work-orders/{workOrder}/status', [WorkOrderController::class, 'updateStatus'])->name('work-orders.update-status');
    });
    Route::middleware('permission:work-orders.delete')->group(function () {
        Route::delete('work-orders/{workOrder}', [WorkOrderController::class, 'destroy'])->name('work-orders.destroy');
    });
    Route::middleware('permission:work-orders.print')->group(function () {
        Route::get('work-orders/{workOrder}/print', [WorkOrderController::class, 'print'])->name('work-orders.print');
        Route::get('work-orders/{workOrder}/print-detailed', [WorkOrderController::class, 'printDetailed'])->name('work-orders.print-detailed');
        Route::get('work-orders/{workOrder}/print-summary', [WorkOrderController::class, 'printSummary'])->name('work-orders.print-summary');
    });

    // Order Batches
    Route::middleware('permission:order-batches.create')->group(function () {
        Route::get('order-batches/create', [OrderBatchController::class, 'create'])->name('order-batches.create');
        Route::post('order-batches', [OrderBatchController::class, 'store'])->name('order-batches.store');
    });
    Route::middleware('permission:order-batches.view')->group(function () {
        Route::get('order-batches', [OrderBatchController::class, 'index'])->name('order-batches.index');
        Route::get('order-batches/{orderBatch}', [OrderBatchController::class, 'show'])->name('order-batches.show');
    });
    Route::middleware('permission:order-batches.edit')->group(function () {
        Route::get('order-batches/{orderBatch}/edit', [OrderBatchController::class, 'edit'])->name('order-batches.edit');
        Route::put('order-batches/{orderBatch}', [OrderBatchController::class, 'update'])->name('order-batches.update');
    });
    Route::middleware('permission:order-batches.delete')->group(function () {
        Route::delete('order-batches/{orderBatch}', [OrderBatchController::class, 'destroy'])->name('order-batches.destroy');
    });
    Route::middleware('permission:order-batches.print')->group(function () {
        Route::get('order-batches/{orderBatch}/print', [OrderBatchController::class, 'print'])->name('order-batches.print');
    });

    // Attributes
    Route::middleware('permission:attributes.create')->group(function () {
        Route::get('attributes/create', [AttributeController::class, 'create'])->name('attributes.create');
        Route::post('attributes', [AttributeController::class, 'store'])->name('attributes.store');
    });
    Route::middleware('permission:attributes.view')->group(function () {
        Route::get('attributes', [AttributeController::class, 'index'])->name('attributes.index');
        Route::get('attributes/{attribute}', [AttributeController::class, 'show'])->name('attributes.show');
    });
    Route::middleware('permission:attributes.edit')->group(function () {
        Route::get('attributes/{attribute}/edit', [AttributeController::class, 'edit'])->name('attributes.edit');
        Route::put('attributes/{attribute}', [AttributeController::class, 'update'])->name('attributes.update');
    });
    Route::middleware('permission:attributes.delete')->group(function () {
        Route::delete('attributes/{attribute}', [AttributeController::class, 'destroy'])->name('attributes.destroy');
    });

    // Category Attributes
    Route::middleware('permission:category-attributes.create')->group(function () {
        Route::get('category-attributes/create', [CategoryAttributeController::class, 'create'])->name('category-attributes.create');
        Route::post('category-attributes', [CategoryAttributeController::class, 'store'])->name('category-attributes.store');
    });
    Route::middleware('permission:category-attributes.view')->group(function () {
        Route::get('category-attributes', [CategoryAttributeController::class, 'index'])->name('category-attributes.index');
    });
    Route::middleware('permission:category-attributes.edit')->group(function () {
        Route::get('category-attributes/{category_attribute}/edit', [CategoryAttributeController::class, 'edit'])->name('category-attributes.edit');
        Route::put('category-attributes/{category_attribute}', [CategoryAttributeController::class, 'update'])->name('category-attributes.update');
    });
    Route::middleware('permission:category-attributes.delete')->group(function () {
        Route::delete('category-attributes/{category_attribute}', [CategoryAttributeController::class, 'destroy'])->name('category-attributes.destroy');
    });
});

<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\ClientController;
use App\Http\Controllers\Api\V1\OrderBatchController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\ReportController;
use App\Http\Controllers\Api\V1\WorkOrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.')->group(function (): void {

    // Categories
    Route::apiResource('categories', CategoryController::class);

    // Products
    Route::apiResource('products', ProductController::class);

    // Clients
    Route::apiResource('clients', ClientController::class);

    // Work Orders
    Route::apiResource('work-orders', WorkOrderController::class);
    Route::patch('work-orders/{id}/status', [WorkOrderController::class, 'updateStatus'])
        ->name('work-orders.update-status');

    // Order Batches
    Route::apiResource('order-batches', OrderBatchController::class);
    Route::post('order-batches/{id}/work-orders', [OrderBatchController::class, 'addWorkOrder'])
        ->name('order-batches.add-work-order');

    // Reports
    Route::prefix('reports')->name('reports.')->group(function (): void {
        Route::get('detailed', [ReportController::class, 'detailed'])->name('detailed');
        Route::get('aggregated', [ReportController::class, 'aggregated'])->name('aggregated');
        Route::get('daily-summary', [ReportController::class, 'dailySummary'])->name('daily-summary');
        Route::get('batch-summary/{id}', [ReportController::class, 'batchSummary'])->name('batch-summary');
    });
});

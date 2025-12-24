<?php

use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SaleController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/inventory', [InventoryController::class, 'store']);
    Route::get('/inventory', [InventoryController::class, 'index']);

    Route::post('/sales', [SaleController::class, 'store']);
    Route::get('/sales/{id}', [SaleController::class, 'show']);

    Route::get('/reports/sales', [ReportController::class, 'sales']);
});

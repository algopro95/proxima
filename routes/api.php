<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoiceItemController;
use App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:api')->group(function () {
    Route::apiResource('invoices', InvoiceController::class);
    Route::get('invoices/{invoice}/items', [InvoiceItemController::class, 'index']);
    Route::post('invoices/{invoice}/items', [InvoiceItemController::class, 'store']);
    Route::get('invoices/{invoice}/items/{item}', [InvoiceItemController::class, 'show']);
    Route::put('invoices/{invoice}/items/{item}', [InvoiceItemController::class, 'update']);
    Route::delete('invoices/{invoice}/items/{item}', [InvoiceItemController::class, 'destroy']);
    Route::post('invoices/{invoice}/pay', [PaymentController::class, 'payInvoice']);
});

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReceiptController;

Route::middleware('auth:consumer')->group(function () {
    Route::post('/receipt/check', [ReceiptController::class, 'check']);
    Route::post('/receipt/claim', [ReceiptController::class, 'claim']);
});
